<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Documentation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/styles/default.min.css">
    <script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/highlight.min.js"></script>
    <style>
        body {
            padding-top: 60px;
        }
        pre {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
        }
        #sidebar {
            width: 250px;
            top: 0;
            left: 0;
            height: 100%;
            overflow-y: auto;
            position: fixed;
        }
        .container {
            margin-left: 260px;
        }
        table {
            width: 100%;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

<nav id="sidebar" class="bg-light">
    <ul class="nav flex-column">
        <li class="nav-item"><a class="nav-link" href="#introduction">Introduction</a></li>
        <li class="nav-item"><a class="nav-link" href="#create-api">Create API</a></li>
        <li class="nav-item"><a class="nav-link" href="#test-api">Test API</a></li>
        <li class="nav-item"><a class="nav-link" href="#success-response">Success Response</a></li>
        <li class="nav-item"><a class="nav-link" href="#error-response">Error Response</a></li>
        <li class="nav-item"><a class="nav-link" href="#attributes-table">Attributes Table</a></li>
    </ul>
</nav>

<div class="container">
    <section id="introduction">
        <h2>Introduction</h2>
        <p>Welcome to the API documentation. Here, you will find information on how to create, test, and manage your API requests and responses effectively.</p>
    </section>

    <section id="create-api">
        <h4>Create a REST API</h4>
        <hr>
        <p>For a Mighty delivery system that operates with a REST API and key-based access, here’s how you might implement and manage API keys for secure access control:</p>

        <div class="text-center mb-3">
            <img src="{{ asset('help1.png') }}" alt="Delivery Image" class="img-fluid">
        </div>

        <p>Select <strong>Add REST API</strong>. You’ll be taken to the Key Details screen.</p>

        <div class="text-center mb-3">
            <img src="{{ asset('help2.png') }}" alt="API Key Details" class="img-fluid">
        </div>
        
        <li>Copy the key for future use.</p>
        <div class="accordion" id="apiAccordion">
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingOne">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        Example of Creating a Paid Order
                    </button>
                </h2>
                <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne">
                    <div class="accordion-body">
                        <pre><code class="json">
                            curl -X POST https://example.com/save-external-order\
                             {
                                "date" : "2022-01-22",
                                "api_token" : "rest_853WW742FV3247AD965ER82R32R0R",
                                "customer":{
                                    "name":"",
                                    "mobile_number":"",
                                    "email_id":""
                                },
                                "pickup_point" : {
                                    "address": "Ved Road Bridge, Surat, Gujarat",
                                    "latitude": "22.3190806",
                                    "longitude": "70.7671176",
                                    "description": "",
                                    "name": "",
                                    "instruction": "",
                                    "contact_number": "+917405895728"
                                },
                                "delivery_point" : {
                                    "address": "Station Road, Khand Bazar, Varachha, Surat, Gujarat, India",
                                    "latitude": "21.2072495",
                                    "longitude": "72.8423258",
                                    "description": "",
                                    "name": "",
                                    "instruction": "",
                                    "contact_number": "+917405895728"
                                },
                                "extra_charges" :[
                                    {
                                        "key": "gst",
                                        "value": 10,
                                        "value_type": "fixed"
                                    },
                                    {
                                        "key": "sgst",
                                        "value": 2,
                                        "value_type": "percentage"
                                    }
                                ],
                                "parcel_type" : "documents",
                                "total_weight" : 5,
                                "parcel_number":1,
                                "payment_collect_from" : "on_delivery",
                                "payment_type" : "online/cash",
                                "packaging_symbols": [
                                    {"title": "Do Not Use Hooks", "key": "do_not_use_hooks"},
                                    {"title": "Bike Delivery", "key": "bike_delivery"},
                                    {"title": "Hazardous Material", "key": "hazardous_material"}
                                ]
                            }</code></pre>
                    </div>
                </div>
            </div>
        </div>
    </br>
        <li>If the customer's email matches an existing record, the ID will be updated. If it's a new user, a new ID will be generated, and the default password will  1 and 8.</li>
</br>
        <div class="accordion" id="apiAccordion">
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingOne">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne1" aria-expanded="true" aria-controls="collapseOne1">
                        Response For Your Order Count Charges Get
                    </button>
                </h2>
                <div id="collapseOne1" class="accordion-collapse collapse show" aria-labelledby="headingOne">
                    <div class="accordion-body">
                        <pre><code class="json">
                            curl -X GET https://example.com/get-code\
                            {
                                "success": true,
                                "distance": 5.78,
                                "total_weight": 5,
                                "total_parcel_number": 1,
                                "delivery_charge": 1,
                                "extra_charges": [
                                    {
                                        "key": "gst",
                                        "value": 5,
                                        "value_type": "percentage"
                                    },
                                    {
                                        "key": "sgst",
                                        "value": 7,
                                        "value_type": "percentage"
                                    }
                                ],
                                "total_amount": 36.99
                            }
                        </code></pre>
                    </div>
                </div>
            </div>
        </div>    
    </section>

    <section id="test-api">
        <h2>Test API</h2>
        <p>Ensure that your test API responses are properly formatted and include all necessary headers for authentication.</p>
    </section>
    <section id="success-response">
        <h2>Get Response Order Charges</h2>
        <p>Success than return response and message</p>
        <pre><code class="json">
            {
                "success": true,
                "distance": 5.78,
                "total_weight": 5,
                "total_parcel_number": 1,
                "delivery_charge": 1,
                "extra_charges": [
                    {
                        "key": "gst",
                        "value": 5,
                        "value_type": "percentage"
                    },
                    {
                        "key": "sgst",
                        "value": 7,
                        "value_type": "percentage"
                    }
                ],
                "total_amount": 36.99
            }
        </code></pre>
    </section>
    <section id="success-response">
        <h2>Success Response</h2>
        <p>Success than return response and message</p>
        <pre><code class="json">
            {
                "order_id": 513,
                "message": "Order has been saved successfully",
                "status": true
            }</code></pre>
    </section>

    <section id="error-response">
        <h2>Error Response</h2>
        <p>When an invalid API token error occurs, the API will return a response with the appropriate status and message.</p>
        <pre><code class="json">
        {
        "message": "Invalid Token",
        "success": false
        }</code></pre>
    </section>
    <section id="error-response">
        <h2>Error Response</h2>
        <p>When a customer's email is null, the API will return a response with an appropriate status and message.</p>
        <pre><code class="json">
            {
                "message": "Email Required",
                "success": false
            }</code></pre>
    </section>

    <section id="error-response">
        <h2>Error Response</h2>
        <p>When a customer's Details is null, the API will return a response with an appropriate status and message.</p>
        <pre><code class="json">
            {
                "message": "Please Fill Customer Details Is Valid",
                "success": false
            }</code></pre>Value
    </section>
    <section id="error-response">
        <h2>Error Response</h2>
        <p>When a Pickup Delivery Details is null, the API will return a response with an appropriate status and message.</p>
        <pre><code class="json">
            {
                "message": "Please Fill Valid Delivery Point",
                "success": false
            }</code></pre>
    </section>
    <section id="error-response">
        <h2>Error Response</h2>
        <p>When a Enter Minus Value, the API will return a response with an appropriate status and message.</p>
        <pre><code class="json">
            {
                "message": "Invalid Value Minus",
                "success": false
            }</code></pre>
    </section>
    <section id="error-response">
        <h2>Error Response</h2>
        <p>When a Enter date formate invalid, the API will return a response with an appropriate status and message.</p>
        <pre><code class="json">
            {
                "message": "The date does not match the format Y-m-d.`",
                "success": false
            }</code></pre>
    </section>
    <section id="error-response">
        <h2>Error Response</h2>
        <p>When a Enter Packaging Symbols Invalid, the API will return a response with an appropriate status and message.</p>
        <pre><code class="json">
            {
                "message": "Packaging_Symbols_Invalid",
                "success": false,
                "invalid_keys": [
                    "recycleppppppppp"
                ]
            }</code></pre>
    </section>

    <section id="attributes-table">
        <h2>API Attributes Table</h2>
        <p>Below are the attributes used in the API request:</p>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Attribute</th>
                    <th>Type</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>date</td>
                    <td>datetime</td>
                    <td>date formate: y-m-d</td>
                </tr>
                <tr>
                    <td>customer</td>
                    <td>Object (JSON)</td>
                    <td>Contains customer information such as name, mobile number, and email ID. || Required Details: email_id</td>
                </tr>
                <tr>
                    <td>pickup_point</td>
                    <td>Object (JSON)</td>
                    <td>Pickup point details including address, latitude, longitude, and contact number. || Required Details:address,latitude,longitude</td>
                </tr>
                <tr>
                    <td>delivery_point</td>
                    <td>Object (JSON)</td>
                    <td>Delivery point details including addresss, latitude, longitude, and contact number. || Required Details:address,latitude,longitude</td>
                </tr>
                <tr>
                    <td>extra_charges</td>
                    <td>Array (JSON)</td>
                    <td>Extra charges for the order, e.g., GST.</td>
                </tr>
                <tr>
                    <td>parcel_type</td>
                    <td>String</td>
                    <td>Type of parcel being shipped, e.g., documents, electronics.</td>
                </tr>
                <tr>
                    <td>total_weight</td>
                    <td>Integer</td>
                    <td>Total weight of the parcel in kilograms.</td>
                </tr>
                <tr>
                    <td>payment_type</td>
                    <td>String</td>
                    <td>Method of payment, either online or cash on delivery. || valid details: cash,online.</td>
                </tr>
                <tr>
                    <td>payment_collect_from</td>
                    <td>String</td>
                    <td>valid details: on_pickup,on_delivery.</td>
                </tr>
                <tr>
                    <td>parcel_number</td>
                    <td>Integer</td>
                    <td>Enter Parcel Number.</td>
                </tr>

                <tr>
                    <td>packaging_symbols</td>
                    <td>Array (JSON)</td>
                    <td>A list of packaging symbols for the parcel, each containing a title and key. || valid details: this_way_up,do_not_stack,temperature_sensitive,do_not_use_hooks,explosive_material,hazardous_material,bike_delivery,keep_dry,perishable,recycle,do_not_open_with_sharp_objects,fragile</td>
                </tr>
            </tbody>
        </table>
    </section>
</div>

<script>
    document.querySelectorAll('a.nav-link').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();

            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        });
    });

    hljs.highlightAll();
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
