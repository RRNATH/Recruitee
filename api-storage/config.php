<?php

return [
     // JWT
    'jwt_secret' => 'H18LNaP9k6XCkvTqbAUnF14N0rYh2PkWHxoaj1LoRjVN+lj0red6j2A64n2G/aAvtLR2Jrv0WMuWkwBC+nkO81u9NFRGrqgCP4H4Dl8XPHYidteb6dOPXcC+KyiTDcZMJp1tlMWDh17lYSa5ceM3+eyYnSv93Bh0SuMR6fMDcmk=',
    'jwt_issuer' => 'localhost',
    'jwt_ttl' => 300, // Token valid for 5 minutes

    //api-storage
    'base_path' => '/api-storage',   // <-- Add base path here
    
     // Recruitee
    'recruitee_base_url' => 'https://api.recruitee.com',
    'recruitee_company_id' => '108812',
    'page_id_token' => [
        // Vacature Aangifte medeweker
        '3569' => 'YjI3QXRKUXcwQmc2MC8wSkwxN25Gdz09',

        // Vacature Beginnend assistent accountant
        '3571' => 'SDQ0Tzk3ZElQcXRVMnFFOW05SlMwdz09',

        // Vacature Controleleider
        '3572' => 'TTlDZElNbzFuUGczYjNMdnRNUzJZZz09',

        // Vacature - Ook starten in de accountancy?
        '3573' => 'YkRyVysrSHI3R3QrTlNCWFpnMmd0dz09',
    ],
        'page_id_offer_id' => [
        '3569' => '2098174',
        '3571' => '2423596',
        '3572' => '2094207',
        '3573' => '2423570',
    ],
];