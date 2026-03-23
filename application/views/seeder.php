<div class="row justify-content-center">
    <div class="col-md-6">

        <div id="seederCard" class="card shadow-lg rounded-4 text-center border-0">
            <div class="card-body p-5">

              
                <div id="statusIcon" class="mb-3" style="font-size: 50px;">
                   Please wait...
                </div>

                <!-- Title -->
                <h3 id="seederTitle" class="mb-3"><?= $title ?></h3>

                <!-- Message -->
                <p id="seederMessage" class="mb-4"><?= $message ?></p>

                <!-- Button -->
                <a href="<?= base_url() ?>" class="btn btn-light d-none" id="homeBtn">
                    Go Home
                </a>

            </div>
        </div>

    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {

    let status  = "<?= $status ?>";
    let message = "<?= addslashes($message) ?>";

    let card  = document.getElementById("seederCard");
    let icon  = document.getElementById("statusIcon");
    let btn   = document.getElementById("homeBtn");

    if (!card) return;

    // Reset
    card.classList.remove("bg-success", "bg-danger", "bg-warning", "text-white");

    if (status === "success") {

        card.classList.add("bg-success", "text-white");
        icon.innerHTML = "✅";

       

    } else if (status === "warning") {

        card.classList.add("bg-warning");
        icon.innerHTML = "⚠️";

      

    } else {

        card.classList.add("bg-danger", "text-white");
        icon.innerHTML = "❌";

    }

    // Show button after popup
    setTimeout(() => {
        btn.classList.remove("d-none");
    }, 800);

});
</script>