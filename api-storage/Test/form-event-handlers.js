 document.addEventListener('wpcf7mailsent', function (event) {
            const page_id = event.detail.contactFormId;

            const candidateData = {
                                    "candidate": {
                                        "name": "John Dave 1516 test 1",
                                        "emails": ["Dave1516@example.com"],
                                        "phones": ["123123123", "767676767676"],
                                        "social_links": ["https://www.facebook.com/johnexamplecom/"],
                                        "links": ["https://www.example.com/"],
                                        "cover_letter": "Example cover letter",
                                        "page_id":"1516"
                                    },
                                    "offer_id": "487965"
            };

            log('Fetching token...');

            fetch('http://localhost/api-storage/index.php/token', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Token API failed');
                }
                return response.json();
            })
            .then(tokenResponse => {
                const token = tokenResponse.token;

                if (!token) {
                    throw new Error('Token missing');
                }

                log('Token received. Creating candidate...');

                return fetch('http://localhost/api-storage/index.php/candidates/create', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(candidateData)
                });
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Candidate API failed');
                }
                return response.json();
            })
            .then(result => {
                log('SUCCESS:\n' + JSON.stringify(result, null, 2));
            })
            .catch(error => {
                log('ERROR:\n' + error.message);
            });
});