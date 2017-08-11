# Extended Version of BeSimpleSoap

Here you go, soap lovers. A lot of new features are extended to BesimpleSoap.

### New Features (Configurations)
#### SoapClient
- Loading wsdl locally and using different url to make request (not to local wsdl file)
- Basic Http Authentication Support

#### SoapServer
- Document-Wrapped support
- Configurable wsdl definition name attribute
- Ws Security with signing the response 
- Multiple namespace support

### Installation

Tell composer to install
```bash
composer require codebach/soap
```

### Usage

Example full configuration:
```yaml
be_simple_soap:
    clients:
        # Wsdl file and request url same place (Basic Usage)
        FooService:
            wsdl: wsdl_service_url

        # Wsdl file and request url different place
        BarService:
            wsdl: wsdl_file_to_load
            request_url:  url_to_make_soap_requests
            basic_http_auth:
                login: login
                password: password

    services:
        # Single Namespace (Basic Usage)
        FooServer:
            namespace:     namespace
            binding:       document-wrapped # Or rpc-literal
            version:       2
            resource:      "@FooBundle/Controller/FooController.php"
            resource_type: annotation
            cache_type:    none

        # Multiple Namespace
        BarServer:
            namespace:      namespace
            binding:        document-wrapped # Or rpc-literal
            version:        2
            resource:       '@BarBundle/Controller/BarController.php'
            resource_type:  annotation
            cache_type:     none
            target_name:    ns1 # Service definition name attribute
            public_key:     public_key_to_sign_response
            private_key:    private_key_to_sign_response
            namespace_types:
              - { name: 'ns2', url: name_space2_url}
              - { name: 'ns3', url: name_space3_url}
```

#### Multiple Namespace Usage

To put the ComplexType in different (e.g: configured in yaml file above), use new Annotation class `BeSimple\SoapBundle\ServiceDefinition\Annotation\Type` and new parameter `target` of `BeSimple\SoapBundle\ServiceDefinition\Annotation\ComplexType` class

Make the server configuration:
```yaml
be_simple_soap:
    services:
        FooServer:
            namespace:      default_namespace
            binding:        document-wrapped
            version:        2
            resource:       '@FooBundle/Controller/DefaultController.php'
            resource_type:  annotation
            cache_type:     none
            target_name:    ns1
            namespace_types:
              - { name: 'ns2', url: 'foo_namespace'}
              - { name: 'ns3', url: 'bar_namespace'}
```

Add Method:
```php
use FooBundle\Server\SoapRequest;
use BeSimple\SoapBundle\ServiceDefinition\Annotation as Soap;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * @Soap\Method("soapRequest")
     * @Soap\Param("request", phpType = "FooBundle\Server\SoapRequest")
     * @Soap\Result("soapResponse", phpType = "FooBundle\Server\SoapResponse")
     */
    public function soapRequestAction(SoapRequest $request)
    {
    }
}
```

Bar.php
```php
use BeSimple\SoapBundle\ServiceDefinition\Annotation as Soap;

/**
 * @Soap\Alias("Bar")
 * @Soap\Type("ns3")
 */
class Bar
{
    /**
     * @var string
     *
     * @Soap\ComplexType("string")
     */
    private $value;

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue(string $value)
    {
        $this->value = $value;
    }
}
```

Foo.php
```php
use BeSimple\SoapBundle\ServiceDefinition\Annotation as Soap;

/**
 * @Soap\Alias("Foo")
 * @Soap\Type("ns2")
 */
class Foo
{
    /**
     * @var Bar
     *
     * @Soap\ComplexType("FooBundle\Server\Bar", target="ns3")
     */
    private $bar;

    /**
     * @return Bar
     */
    public function getBar(): Bar
    {
        return $this->bar;
    }

    /**
     * @param Bar $bar
     */
    public function setBar(Bar $bar)
    {
        $this->bar = $bar;
    }
}

```

SoapRequest.php
```php
use BeSimple\SoapBundle\ServiceDefinition\Annotation as Soap;

/**
 * @Soap\Alias("SoapRequest")
 *
 * Default namespace is ns1
 */
class SoapRequest
{
    /**
     * @var Foo
     *
     * @Soap\ComplexType("FooBundle\Server\Foo", target="ns2")
     */
    private $foo;

    /**
     * @return Foo
     */
    public function getFoo(): Foo
    {
        return $this->foo;
    }

    /**
     * @param Foo $foo
     */
    public function setFoo(Foo $foo)
    {
        $this->foo = $foo;
    }
}
```

And here is the xml output of our server:
```xml
<definitions xmlns="http://schemas.xmlsoap.org/wsdl/"
             xmlns:tns="default_namespace"
             xmlns:ns="default_namespace/types"
             xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/"
             xmlns:xsd="http://www.w3.org/2001/XMLSchema"
             xmlns:soapenc="http://schemas.xmlsoap.org/soap/encoding/"
             xmlns:ns2="foo_namespace"
             xmlns:ns3="bar_namespace"
             name="ns1"
             targetNamespace="default_namespace">
    <types>
        <xsd:schema xmlns:ns2="foo_namespace" xmlns:ns3="bar_namespace" targetNamespace="default_namespace" elementFormDefault="qualified" attributeFormDefault="unqualified">
            <xsd:complexType name="SoapRequest">
                <xsd:sequence>
                    <xsd:element name="foo" type="ns2:Foo"/>
                </xsd:sequence>
            </xsd:complexType>
            <xsd:complexType name="AppBundle.Server.SoapResponse">
                <xsd:sequence/>
            </xsd:complexType>
            <xsd:element name="soapRequest" type="tns:soapRequest"/>
            <xsd:element name="soapRequestResponse" type="tns:soapRequestResponse"/>
        </xsd:schema>


        <xsd:schema xmlns:ns2="foo_namespace" targetNamespace="foo_namespace" elementFormDefault="qualified" attributeFormDefault="unqualified">
            <xsd:complexType name="Foo">
                <xsd:sequence>
                    <xsd:element name="bar" type="ns3:Bar"/>
                </xsd:sequence>
            </xsd:complexType>
        </xsd:schema>


        <xsd:schema xmlns:ns3="bar_namespace" targetNamespace="bar_namespace" elementFormDefault="qualified" attributeFormDefault="unqualified">
            <xsd:complexType name="Bar">
                <xsd:sequence>
                    <xsd:element name="value" type="xsd:string"/>
                </xsd:sequence>
            </xsd:complexType>
        </xsd:schema>
    </types>


    <portType name="ns1PortType">
        <operation name="soapRequest">
            <input message="tns:soapRequest"/>
            <output message="tns:soapRequestResponse"/>
        </operation>
    </portType>


    <message name="soapRequest">
        <part name="parameters" element="tns:soapRequest"/>
    </message>
    <message name="soapRequestResponse">
        <part name="parameters" element="tns:soapRequestResponse"/>
    </message>


    <service name="ns1">
        <port name="ns1Port" binding="tns:ns1Binding">
            <soap:address location="http://localhost/Symfony/Symfony28/web/app_dev.php/ws/v1/FooServer"/>
        </port>
    </service>


    <binding name="ns1Binding" type="tns:ns1PortType">
        <soap:binding transport="http://schemas.xmlsoap.org/soap/http" style="document"/>
        <operation name="soapRequest">
            <soap:operation soapAction="default_namespace/soapRequest"/>
            <input>
                <soap:body use="literal" namespace="default_namespace" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/>
            </input>
            <output>
                <soap:body use="literal" namespace="default_namespace" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/>
            </output>
        </operation>
    </binding>
</definitions>
```


### Components

BeSimpleSoap consists of five components ...

#### BeSimpleSoapBundle

The BeSimpleSoapBundle is a Symfony2 bundle to build WSDL and SOAP based web services.
For further information see the [README](https://github.com/codebach/Soap/blob/master/src/BeSimple/SoapBundle/README.md).

#### BeSimpleSoapClient

The BeSimpleSoapClient is a component that extends the native PHP SoapClient with further features like SwA, MTOM and WS-Security.
For further information see the [README](https://github.com/codebach/Soap/blob/master/src/BeSimple/SoapClient/README.md).

#### BeSimpleSoapCommon

The BeSimpleSoapCommon component contains functionylity shared by both the server and client implementations.
For further information see the [README](https://github.com/codebach/Soap/blob/master/src/BeSimple/SoapCommon/README.md).

#### BeSimpleSoapServer

The BeSimpleSoapServer is a component that extends the native PHP SoapServer with further features like SwA, MTOM and WS-Security.
For further information see the [README](https://github.com/codebach/Soap/blob/master/src/BeSimple/SoapServer/README.md).

#### BeSimpleSoapWsdl

For further information see the [README](https://github.com/codebach/Soap/blob/master/src/BeSimple/SoapWsdl/README.md).

