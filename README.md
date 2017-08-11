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
        slup_service:
            wsdl: wsdl_service_url

        # Wsdl file and request url different place
        fc_bayern_service:
            wsdl: wsdl_file_to_load
            request_url:  url_to_make_soap_requests
            basic_http_auth:
                login: login
                password: password

    services:
        # Single Namespace (Basic Usage)
        slupClient:
            namespace:     namespace
            binding:       document-wrapped # Or rpc-literal
            version:       2
            resource:      "@FooBundle/Controller/FooController.php"
            resource_type: annotation
            cache_type:    none

        # Multiple Namespace
        capStore:
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

