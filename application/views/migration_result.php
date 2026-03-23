<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-lg border-0 rounded-4 text-center">
            <div class="card-body p-5">
                <h3 class="mb-3">Database Migration</h3>
                <p class="text-muted" id="migation">Please wait while we process...</p>
                <!-- loder -->
                <div class="spinner-border text-primary mt-3" role="status" id="loding_bar"></div>
                <!-- Button -->
                <a href="<?= base_url() ?>" class="btn btn-light d-none" id="homeBtn">
                    Go Home
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {

        let status = "<?= $status ?>";
        let message = "<?= addslashes($message) ?>";
       if (status === "success") {
            $("#migation").html("Migration Done");
            $("#migation").addClass("card bg-success text-white");
            $("#migation").removeClass("text-muted");

        } else {
            $("#migation").html("Migration failed");
            $("#migation").addClass("card bg-danger text-white ");
            $("#migation").removeClass("text-muted");

        }
        $("#loding_bar").hide();
        setTimeout(() => {
             $("#homeBtn").removeClass("d-none");
          
        }, 800);
    });
</script>