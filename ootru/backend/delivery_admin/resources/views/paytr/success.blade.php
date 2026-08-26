<x-guest-layout>
    <style>
        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        
        .responsive-image {
            width: 25%;
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
    <div class="container text-center p-5 d-flex justify-content-center align-items-center vh-100">
        <img src="{{ asset('public/images/paytr_success.png') }}" class="responsive-image mb-4" alt=""> 
    </div>
</x-guest-layout>