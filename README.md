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

Build SOAP and WSDL based web services

### Installation

Tell composer to install
```bash
composer require codebach/soap
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

