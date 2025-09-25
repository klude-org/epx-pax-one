<?php


// Function to validate date (format YYYY-MM-DD or YYYY-MM-DD HH:MM:SS)
function validate_date($date) {
    $dateRegex = '/^\d{4}-\d{2}-\d{2}$/'; // For date (YYYY-MM-DD)
    $datetimeRegex = '/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/'; // For datetime (YYYY-MM-DD HH:MM:SS)
    return preg_match($dateRegex, $date) || preg_match($datetimeRegex, $date);
}


if($action = $_REQUEST['--action'] ?? null){
    switch($action){
        case 'table-data': {
            // Get table name
            $table = $_REQUEST['table'] ?? '';
            if (!$table) {
                echo json_encode(["error" => "No table specified"]);
                exit;
            }

            $pdo = \_\db();
            $draw   = $_REQUEST['draw'] ?? 1;
            $start  = $_REQUEST['start'] ?? 0;
            $length = $_REQUEST['length'] ?? 10;
            $search = $_REQUEST['search']['value'] ?? '';
            $orderColumnIndex = $_REQUEST['order'][0]['column'] ?? 1;
            $orderDir = $_REQUEST['order'][0]['dir'] ?? 'asc';

            // Get column names and types
            if($stmt = $pdo->query("SHOW COLUMNS FROM `$table`")){
                $columns = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch column info with types
            } else {
                $columns = [];
            }

            if (empty($columns)) {
                echo json_encode(["error" => "No columns found"]);
                exit;
            }

            $columnNames = array_column($columns, 'Field'); // Extract column names
            $columnTypes = array_column($columns, 'Type'); // Extract column types

            // Validate order column (offset by 1 because of Serial No column)
            $orderColumn = $columnNames[$orderColumnIndex - 1] ?? $columnNames[0];

            // Base SQL
            $sql = "SELECT * FROM `$table`";
            $countSql = "SELECT COUNT(*) FROM `$table`";

            $searchConditions = [];
            $searchParams = [];

            // Search filter
            if ($search) {
                foreach ($columns as $index => $col) {
                    $columnName = $col['Field'];
                    $columnType = $col['Type'];

                    // Check if the column is a DATE, DATETIME, or TIMESTAMP type
                    if (strpos($columnType, 'date') !== false || strpos($columnType, 'datetime') !== false || strpos($columnType, 'timestamp') !== false) {
                        // Check if search is a valid date or timestamp (in format YYYY-MM-DD or YYYY-MM-DD HH:MM:SS)
                        if (validate_date($search)) {
                            $searchConditions[] = "`$columnName` = :search$index";
                            $searchParams[":search$index"] = $search;
                        }
                    } else {
                        $searchConditions[] = "`$columnName` LIKE :search$index";
                        $searchParams[":search$index"] = "%$search%";
                    }
                }

                $searchSql = " WHERE " . implode(" OR ", $searchConditions);
                $sql .= $searchSql;
                $countSql .= $searchSql;
            }

            // Order and limit
            $sql .= " ORDER BY `$orderColumn` $orderDir LIMIT :start, :length";

            // Count total records
            $stmt = $pdo->prepare($countSql);
            foreach ($searchParams as $param => $value) {
                $stmt->bindValue($param, $value, PDO::PARAM_STR);
            }
            $stmt->execute();
            $totalRecords = $stmt->fetchColumn();

            // Fetch paginated data
            $stmt = $pdo->prepare($sql);
            foreach ($searchParams as $param => $value) {
                $stmt->bindValue($param, $value, PDO::PARAM_STR);
            }
            $stmt->bindValue(':start', (int)$start, PDO::PARAM_INT);
            $stmt->bindValue(':length', (int)$length, PDO::PARAM_INT);
            $stmt->execute();

            $data = [];
            $serialNumber = $start + 1; // Adjust serial number based on pagination start

            while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
                array_unshift($row, $serialNumber++); // Add serial number at the beginning
                $data[] = $row;
            }

            // Prepare response
            $response = [
                "draw"            => intval($draw),
                "recordsTotal"    => $totalRecords,
                "recordsFiltered" => $totalRecords,
                "data"            => $data
            ];

            header('Content-Type: application/json');
            echo json_encode($response);
            exit();
        };
    }
}

?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <style>
        html, body { height: 100%; margin: 0; }
        table, .table, .table td, .table th, .list-group-item { font-family: monospace; }
        .table td, .table th { white-space: nowrap; overflow: auto; padding: .2rem .3rem; line-height: 1.1; vertical-align: middle; }
        .table tr { height: auto; }
        body .app-shell { display: block; }
        body .sidebar, body .resize-handle, body .main { display: none !important; }
        body .legacy-main { display: block !important; padding: 1rem; }

        /* Full-height embed layout */
        body .legacy-main{
            height:100vh;
            display:flex;
            flex-direction:column;
            padding:.75rem;
        }

        /* Make the DataTables wrapper fill and behave as a column */
        body .dataTables_wrapper{
            display:flex;
            flex-direction:column;
            min-height:0;
            flex:1;
        }

        /* The scrollable table body should consume remaining height */
        body div.dataTables_scroll{
            display:flex;
            flex-direction:column;
            min-height:0;
            flex:1;
        }
        body div.dataTables_scrollBody{
            flex:1 !important;
            min-height:0 !important;
            overflow:auto !important;
        }

        /* Push the bottom controls (info + paginate) to the bottom */
        body .dataTables_wrapper .row:last-child{
            margin-top:auto; /* anchors bottom row */
        }
        
        .dataTables_scroll {
            width: calc(100vw - 40px);
            height: calc(100vh - 160px);
            overflow: auto;
        }


    </style>
</head>
<body>
    <div class="app-shell">
        <div class="container-fluid p-4 legacy-main">
            <h2 class="mb-3"><?= $_REQUEST['table'] ?? '??' ?></h2>
            <?php if (!empty($_REQUEST['table'])): ?>
                <div class="table-container">
                    <table id="dataTable" class="table table-dark table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <?php foreach (\_\db()->table_column__list($_REQUEST['table']) ?? [] as $col): ?>
                                    <th><?= htmlspecialchars($col) ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                    </table>
                </div>
            <?php else: ?>
                <p>Please select a table from the sidebar.</p>
            <?php endif; ?>
        </div>
    </div>
    

    <!-- Error Modal -->
    <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="errorModalLabel">Error</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="errorModalBody"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
    // xui error modal helper (from original)
    window.xui = window.xui || {};
    xui.error_modal = {
        show(title, message) {
            $('#errorModalLabel').text(title);
            $('#errorModalBody').html('<pre>' + (message || '') + '</pre>');
            $('#errorModal').modal('show');
        }
    };
    const X_CSRF = <?php echo json_encode(constant('_\CSRF')); ?>;
    document.addEventListener('DOMContentLoaded', function() {
        $(document).ready(function() {
            $('#dataTable').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "?--action=table-data&table=<?= $_REQUEST['table'] ?? '' ?>",
                    "type": "POST",
                    "error": function(xhr, status, error) {
                        xui.error_modal.show("Error loading data", xhr.responseText || error);
                    },
                    "headers": {
                        'X-Csrf-Token': X_CSRF   // 🔑 add your header
                    }
                },
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
                "scrollCollapse": true,
                scrollX: true,            // helps with nowrap cells
                scrollCollapse: true,
                dom:                 // puts length+filter on top, info+paginate at bottom
                    "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                    "<'row'<'col-sm-12 col-md-6't>>" +
                    "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                
                // dynamic scrollY so table body fills available space
                scrollY: (function(){
                    try {
                    var legacy = document.querySelector(".legacy-main");
                    var avail = legacy ? legacy.clientHeight : window.innerHeight;
                    var estTop = 64;   // l + f controls (rough)
                    var estBottom = 56; // i + p controls (rough)
                    var estHead = 48;   // table header
                    var padding = 24;
                    var px = Math.max(240, avail - estTop - estBottom - estHead - padding);
                    return px + "px";
                    } catch(e){ return "60vh"; }
                })()
            }).on('error.dt', function(e, settings, techNote, message) {
                xui.error_modal.show("DataTable Error", message);
            });
        });
    });
    </script>
</body>
</html>