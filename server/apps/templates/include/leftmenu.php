<nav>
    <ul>
        <li <?php if ($this->isActiveMenu('trial', 'index')){ ?>class="active"<?php } ?>>
            <a href="/trial/index/" title="Dashboard"><i class="fa fa-lg fa-fw fa-home"></i> <span
                    class="menu-item-parent">编译源代码</span></a>
        </li>

        <li <?php if ($this->isActiveMenu('trial', 'download')){ ?>class="active"<?php } ?>>
            <a href="/trial/download/" title="Dashboard"><i class="fa fa-lg fa-fw fa-paste"></i> <span
                    class="menu-item-parent">下载 Loader</span></a>
        </li>
        <?php if (in_array($_SESSION['user']['id'], Swoole::$php->config['common']['admin_uids'])) { ?>
        <li <?php if ($this->isActiveMenu('trial', 'balance')){ ?>class="active"<?php } ?>>
            <a href="/trial/balance/" title="Dashboard"><i class="fa fa-lg fa-fw fa-cloud-upload"></i> <span
                    class="menu-item-parent">开通试用</span></a>
        </li>
        <?php } ?>

        <li <?php if ($this->isActiveMenu('crm', 'invoice')){ ?>class="active"<?php } ?>>
            <a href="/crm/invoice/" title="Dashboard"><i class="fa fa-lg fa-fw fa-tasks"></i> <span
                    class="menu-item-parent">发票申请</span></a>
        </li>
    </ul>

</nav>
