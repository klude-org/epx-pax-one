    <?php $this->view->wrap_in("#/_/theme/page/polec_4"); ?>
    <form action="" method="POST">
        <input type="hidden" name="--csrf" value="<?=\_\CSRF?>">
        <div class="container-fluid sticky-top mt-1" style="z-index:1">
            <div class="row">
                <div class="col">
                    <div class="float-end">
                        <div class="btn-group">
                            <input type="submit" class="btn btn-outline-primary btn-sm" name="--action" value="Save">
                            <?php if($this->o->db_is_connected):?>
                            <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="visually-hidden">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu">
                                <li><input type="submit" class="dropdown-item" style="cursor:pointer" name="--action" value="Backup"></li>
                                <li><input type="submit" class="dropdown-item" style="cursor:pointer" name="--action" value="Load"></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><input type="submit" class="dropdown-item" style="cursor:pointer" name="--action" value="Clear"></li>
                            </ul>
                            <?php endif;?>
                        </div>                                    
                    </div>
                </div>
                <input type="hidden" name="--cfg" value="<?=$_GET['--cfg'] ?? ''?>">
            </div>
        </div>
        <div class="container-fluid mt-1">
            <div class="row">
                <div class="col">
                    <div class="alert alert-<?=$this->o->alert_type?>" role="alert">
                        <?= $this->o->alert_message ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="form-group mb-3">
                        <label for="ff(FW_DB_HOSTNAME)" class="form-label">Host</label>
                        <input type="text" class="form-control" id="ff(FW_DB_HOSTNAME)" name="ff[FW_DB_HOSTNAME]" placeholder="..." value="<?=$_ENV['DB_HOSTNAME'] ?? ''?>">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="form-group mb-3">
                        <label for="ff(FW_DB_USERNAME)" class="form-label">Username</label>
                        <input type="text" class="form-control" id="ff(FW_DB_USERNAME)" name="ff[FW_DB_USERNAME]" placeholder="..." value="<?=$_ENV['DB_USERNAME'] ?? ''?>">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="form-group mb-3">
                        <label for="ff(FW_DB_PASSWORD)" class="form-label">Password</label>
                        <input type="text" class="form-control" id="ff(FW_DB_PASSWORD)" name="ff[FW_DB_PASSWORD]" placeholder="..." value="<?=$_ENV['DB_PASSWORD'] ?? ''?>">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="form-group mb-3">
                        <label for="ff(FW_DB_DATABASE)" class="form-label">Database</label>
                        <input type="text" class="form-control" id="ff(FW_DB_DATABASE)" name="ff[FW_DB_DATABASE]" placeholder="..." value="<?=$_ENV['DB_DATABASE'] ?? ''?>">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="form-group mb-3">
                        <label for="ff(FW_DB_CHAR_SET)" class="form-label">Char Set</label>
                        <input type="text" class="form-control" id="ff(FW_DB_CHAR_SET)" name="ff[FW_DB_CHAR_SET]" placeholder="..." value="<?=$_ENV['DB_CHAR_SET'] ?? ''?>">
                    </div>
                </div>
            </div>                           
        </div>
    </form>
    <?php if($this->o->db_is_connected): ?>
        <div class="container-fluid">
            <ul class="list-group mt-3">
                <?php 
                    $list = \glob(\_\DATA_DIR."/db-backup-*.json");
                    \rsort($list);
                    if(\is_file($latest = \_\DATA_DIR."/db-backup.json")){
                        \array_unshift($list, $latest);
                    }
                    foreach($list as $f): ?>
                    <li class="list-group-item">
                        <span title="<?=\hash_file("md5",$f)?>"><?=\basename($f)?></span>
                        <form class="float-end" action="" method="POST">
                            <input hidden name="--csrf" value="<?=\_\CSRF?>">
                            <input type="hidden" name="file" value="<?=\basename($f)?>">
                            <div class="float-end">
                                <div class="btn-group">
                                    <input type="submit" class="btn btn-outline-primary btn-sm" name="--action" value="Restore" onclick="return confirm('This will change the database!!!\nAre you sure?')) ? true : event.preventDefault()">
                                    <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                                        <span class="visually-hidden">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><input type="submit" class="dropdown-item" style="cursor:pointer" name="--action" value="Download"></li>
                                        <li><input type="submit" class="dropdown-item" style="cursor:pointer" name="--action" value="Remove"></li>
                                    </ul>
                                </div>                                    
                            </div>
                        </form>
                    </li>
                <?php endforeach ?>
            </ul>
        </div>
    <?php elseif(\_\f('db/schema.sql', true)): ?>
        <form action="" method="POST">
            <div class="card">
                <div class="card-body">
                    <input type="submit" class="btn btn-primary" name="--action" value="Initialize">
                </div>
            </div>
        </form>
    <?php else: ?>
        <div class="text-center">
            This is not a valid database
        </div>
    <?php endif ?>

