<?php
/*
 * This file is part of the BeSimpleSoapBundle.
 *
 * (c) Christian Kerl <christian-kerl@web.de>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace BeSimple\SoapBundle\ServiceBinding;

use BeSimple\SoapBundle\ServiceDefinition\Method;
use BeSimple\SoapCommon\Definition\Type\TypeRepository;

/**
 * @author Christian Kerl <christian-kerl@web.de>
 */
class DocumentLiteralWrappedResponseMessageBinder implements MessageBinderInterface
{
    protected $typeRepository;

    public function processMessage(Method $messageDefinition, $message, TypeRepository $typeRepository)
    {
        $this->typeRepository = $typeRepository;

        $result = new \stdClass();
        //$result->{$messageDefinition->getName().'Result'} = $message;
        foreach ($messageDefinition->getOutput()->all() as $name => $part) {
            //$result->{$name} = $message;
            $result = $this->processType($part->getType(), $message);
            break; // only one iteration
        }

        return $result;
    }

    private function processType($phpType, $message)
    {
        $isArray = false;

        $type = $this->typeRepository->getType($phpType);
        if ($type instanceof ArrayOfType) {
            $isArray = true;

            $type = $this->typeRepository->getType($type->get('item')->getType());
        }

        if ($type instanceof ComplexType) {
            $phpType = $type->getPhpType();

            if ($isArray) {
                $array = array();

                // See https://github.com/BeSimple/BeSimpleSoapBundle/issues/29
                if (is_array($message) && in_array('BeSimple\SoapCommon\Type\AbstractKeyValue', class_parents($phpType))) {
                    $keyValue = array();
                    foreach ($message as $key => $value) {
                        $keyValue[] = new $phpType($key, $value);
                    }

                    $message = $keyValue;
                }

                foreach ($message as $complexType) {
                    $array[] = $this->checkComplexType($phpType, $complexType);
                }

                $message = $array;
            } else {
                $message = $this->checkComplexType($phpType, $message);
            }
        }

        return $message;
    }

    private function checkComplexType($phpType, $message)
    {
        $hash = spl_object_hash($message);
        if (isset($this->messageRefs[$hash])) {
            return $this->messageRefs[$hash];
        }

        $this->messageRefs[$hash] = $message;

        if (!$message instanceof $phpType) {
            throw new \InvalidArgumentException(sprintf('The instance class must be "%s", "%s" given.', $phpType, get_class($message)));
        }

        $messageBinder = new MessageBinder($message);
        foreach ($this->typeRepository->getType($phpType)->all() as $type) {
            $property = $type->getName();
            $value = $messageBinder->readProperty($property);

            if (null !== $value) {
                $value = $this->processType($type->getType(), $value);

                $messageBinder->writeProperty($property, $value);
            }

            if (!$type->isNillable() && null === $value) {
                throw new \InvalidArgumentException(sprintf('"%s::%s" cannot be null.', $phpType, $type->getName()));
            }
        }

        return $message;
    }
}