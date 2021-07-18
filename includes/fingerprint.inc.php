<script>
    function initFingerprintJS() {
        // Initialize an agent at application startup.
        const fpPromise = FingerprintJS.load();


        // Get the visitor identifier when you need it.
        fpPromise
            .then(fp => fp.get())
            .then(result => {
                // This is the visitor identifier:
                const visitorId = result.visitorId;
                console.log(visitorId);
                const xmlhttp = new XMLHttpRequest();

                xmlhttp.open("POST", "/controller/user_controller.php", true);
                xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

                const parameters = "fingerprint_relogin=" + visitorId;

                xmlhttp.onreadystatechange = function () {
                    if (this.readyState == 4 && this.status == 200) {
                        console.log(this.responseText);
                        const result = JSON.parse(this.responseText);
                        if (result["status"] == true) {
                            location.reload();
                        }
                    }
                };

                xmlhttp.send(parameters);

            });
    }
</script>
<script
        async
        src="//cdn.jsdelivr.net/npm/@fingerprintjs/fingerprintjs@3/dist/fp.min.js"
        onload="initFingerprintJS()">
</script>

