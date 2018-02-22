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
                        Transactions
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
                <?php _e("All transactions are logged here." ); ?>
            </div>
        </div>
    </section>
</div>