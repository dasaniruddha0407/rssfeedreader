<style>
    body {
        font-family: Arial;
        background: #f5f7fa;
        margin: 0;
        padding: 20px;
    }

    .container {
        max-width: 800px;
        margin: auto;
        background: #fff;
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    input[type="text"] {
        width: 100%;
        padding: 12px;
        border: 1px solid #ccc;
        border-radius: 6px;
        margin-bottom: 15px;
    }

    /* Cards */
    .cards {
        display: flex;
        gap: 20px;
        margin: 20px 0;
    }

    .card {
        flex: 1;
        border: 2px solid #ddd;
        border-radius: 8px;
        padding: 15px;
        cursor: pointer;
    }

    .card.active {
        border-color: #1abc9c;
        background: #e8f8f5;
    }

    .bars {
        display: flex;
        align-items: flex-end;
        gap: 6px;
        height: 50px;
        margin-top: 10px;
    }

    .bar {
        width: 8px;
        background: #1abc9c;
    }

    .asc .bar:nth-child(1) {
        height: 10px;
    }

    .asc .bar:nth-child(2) {
        height: 20px;
    }

    .asc .bar:nth-child(3) {
        height: 30px;
    }

    .asc .bar:nth-child(4) {
        height: 40px;
    }

    .asc .bar:nth-child(5) {
        height: 50px;
    }

    .desc .bar:nth-child(1) {
        height: 50px;
    }

    .desc .bar:nth-child(2) {
        height: 40px;
    }

    .desc .bar:nth-child(3) {
        height: 30px;
    }

    .desc .bar:nth-child(4) {
        height: 20px;
    }

    .desc .bar:nth-child(5) {
        height: 10px;
    }



    button {
        width: 100%;
        padding: 12px;
        background: #1abc9c;
        color: #fff;
        border: none;
        border-radius: 6px;
        cursor: pointer;
    }

    .error {
        color: red;
        margin-bottom: 10px;
    }
</style>



<h2>Import RSS Feed</h2>

<div class="error" id="error"></div>

<form method="post" id="rssForm" enctype="multipart/form-data" onsubmit="return validateForm()">

    <!-- RSS URL -->
    <input type="text" id="rss_url" name="rss_url" placeholder="Enter RSS Feed URL">



    <!-- Sort Cards -->
    <div class="cards">
        <div class="card asc active" onclick="selectMode('ASC')">
            <strong>Oldest First (ASC)</strong>
            <div class="bars">
                <div class="bar"></div>
                <div class="bar"></div>
                <div class="bar"></div>
                <div class="bar"></div>
                <div class="bar"></div>
            </div>
        </div>

        <div class="card desc" onclick="selectMode('DESC')">
            <strong>Newest First (DESC)</strong>
            <div class="bars">
                <div class="bar"></div>
                <div class="bar"></div>
                <div class="bar"></div>
                <div class="bar"></div>
                <div class="bar"></div>
            </div>
        </div>
    </div>

    <input type="hidden" name="sort_mode" id="sort_mode" value="ASC">

    <button type="submit" id="subtn">Fetch Feed</button>
</form>
</div>

<script>
    // Sort selection
    function selectMode(mode) {
        document.getElementById('sort_mode').value = mode;
        document.querySelectorAll('.card').forEach(c => c.classList.remove('active'));
        document.querySelector('.' + mode.toLowerCase()).classList.add('active');
    }

    // Validation

    function validateForm() {
        let url = document.getElementById('rss_url').value.trim();
        let error = document.getElementById('error');
        let sort_mode = document.getElementById('sort_mode').value;

        error.innerText = "";

        if (!url) {
            toastr.error("RSS Feed URL is required");
            error.innerText = "RSS Feed URL is required";
            return false;
        }

        if (!/^https?:\/\//i.test(url)) {
            toastr.error("Enter valid URL (http/https)");
            error.innerText = "Enter valid URL (http/https)";
            return false;
        }

        if (!/(\.xml|\.rss|feed)/i.test(url)) {
            toastr.warning("URL should be RSS feed (xml/rss/feed)");
            error.innerText = "URL should be RSS feed (xml/rss/feed)";
            return false;
        }

        if (!sort_mode) {
            toastr.error("Please select sorting (ASC / DESC)");
            error.innerText = "Please select sorting";
            return false;
        }

        return true;
    }


    // AJAX Submit
    document.getElementById('rssForm').addEventListener('submit', function(e) {
        e.preventDefault();

        if (!validateForm()) return;

        let formData = new FormData(this);

        toastr.info("Fetching RSS feed...");
        $("#subtn").html("Fetching RSS feed...");
        fetch("<?= base_url('import-feed/import') ?>", {
                method: "POST",
                body: formData
            })
            .then(res => res.json())
            .then(response => {

                if (response.status) {
                    toastr.success(response.message || "Feed Imported Successfully");

                    // reload after success
                    setTimeout(() => {
                        //   window.location.reload();
                    }, 1500);

                } else {
                    toastr.error(response.message || "Import failed");
                }
                $("#subtn").html("Fetch Feed");
            })
            .catch(error => {
                toastr.error("Something went wrong");
                console.error(error);
                $("#subtn").html("Fetch Feed");
            });
    });
</script>