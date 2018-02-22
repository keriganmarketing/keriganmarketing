<?php /*
 Admin Overview page
 */
?>
<div class="page-wrapper" style="margin-left:-20px;">
    <div class="hero is-dark">
        <div class="hero-body">
            <div class="columns is-vcentered">
                <div class="column">
                    <p class="title">
                        KMA Payment Center
                    </p>
                </div>
                <div class="column is-narrow">
                    <?php include('KMASig.php'); ?>
                </div>
            </div>
        </div>
    </div>
    <section class="section">
        <div class="container is-fluid">
            <div class="content">
                <div class="columns is-multiline is-3 is-level">
                    <div class="column is-4 is-1by1">
                        <a class="button is-primary is-block is-medium"
                           href="/wp-admin/admin.php?page=payment-settings"
                           style="white-space: normal; height:100%; align-items: center; justify-content: center; display: flex !important; padding:20% 2rem"
                        >First-time setup options and Authorize.net settings</a>
                    </div>
                    <div class="column is-4 is-1by1">
                        <a class="button is-info is-block is-medium"
                           href="/wp-admin/admin.php?page=payment-services"
                           style="white-space: normal; height:100%; align-items: center; justify-content: center; display: flex !important; padding:20% 2rem"
                        >Create services so your clients can pick from pre-defined options</a>
                    </div>
                    <div class="column is-4 is-1by1">
                        <a class="button is-danger is-block is-medium"
                           href="/wp-admin/admin.php?page=payment-transactions"
                           style="white-space: normal; height:100%; align-items: center; justify-content: center; display: flex !important; padding:20% 2rem"
                        >View transaction history<br>(coming soon)</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>