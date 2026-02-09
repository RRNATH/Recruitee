document.addEventListener('wpcf7mailsent', function (event) {
    console.log('Hello');
            const page_id = event.detail.contactFormId;

            const name  = document.getElementById('name').value;
            const email = document.getElementById('email').value;
            const phone = document.getElementById('phone').value;
            const cover_letter = document.getElementById('extrainfo').value;

            const candidateData = {
                                    "candidate": {
                                        "name": name,
                                        "emails": [email],
                                        "phones": [phone],
                                        "cover_letter": cover_letter,
                                        "page_id": page_id
                                    }
            };


            fetch('https://mk.nl/api-storage/index.php/token', {
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

                return fetch('https://mk.nl/api-storage/index.php/candidates/create', {
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + token,
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
                console.log('SUCCESS:\n' + JSON.stringify(result, null, 2));
            })
            .catch(error => {
                console.log('ERROR:\n' + error.message);
            });
        });