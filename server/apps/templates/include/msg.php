<?php if (!empty($msg)): ?>
<fieldset>
    <section>
        <div class="alert alert-block <?php if ($msg['code'] == 0) echo "alert-success"; else echo 'alert-danger'; ?>">
            <a class="close" data-dismiss="alert" href="#">×</a>
            <h4 class="alert-heading"><i class="fa fa-<?= $msg['code'] == 0 ? 'check-square-o' : 'times-circle-o' ?>"></i>
                <?= $msg['message'] ?><?php if(isset($msg['insert_id'])) echo ' #'.$msg['insert_id'];?></h4>
        </div>
    </section>
</fieldset>
<?php endif; ?>