<x-guest-layout>
    <style>
        .container {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #fff transparent;
            border: 0px;
        }

        .responsive-image {
            width: 25%;
            margin-bottom: 1rem;
        }

        .text-left {
            text-align: left;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
            max-width: 300px; /* optional: set max-width to avoid the table being too wide */
        }

        td {
            padding: 8px;
            text-align: left;
        }

        .no-border {
            border: none;
        }

        .no-border td {
            border: none;
        }

        @media (max-width: 1200px) {
            .responsive-image {
                width: 80%;
            }
        }
        
        @media (max-width: 992px) {
            .responsive-image {
                width: 60%;
            }
        }
        
        @media (max-width: 768px) {
            .responsive-image {
                width: 60%;
            }
        }
        
        @media (max-width: 576px) {
            .responsive-image {
                width: 80%;
            }
        }
        
        @media (max-width: 400px) {
            .responsive-image {
                width: 80%;
            }
        }
    </style>
    <div class="container p-5">
			<img src="{{ asset('public/images/paytr_failed.png') }}" class="responsive-image mb-4" alt=""> 
    </div>
</x-guest-layout>