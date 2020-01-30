<script src='modules/addons/NotificationsDDoSAttacks/templates/js/shared/bootstrap-toggle.min.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js'></script>
<script src='https://cdn.datatables.net/plug-ins/1.10.20/sorting/datetime-moment.js'></script>
<script src='/modules/addons/NotificationsDDoSAttacks/templates/js/client/autoSave.js'></script>
<script src='/modules/addons/NotificationsDDoSAttacks/templates/js/admin/notify.min.js'></script>
<link rel='stylesheet' href='modules/addons/NotificationsDDoSAttacks/templates/css/shared/bootstrap-toggle.min.css'>

<script>
    window.notifications_ddos_attacks = {
        userid: {$userid},
        client_type: "user",
        sign: "{$sign}"
    };
    {literal}
    $(function () {
            $(document).ready(function () {
                var table1 = jQuery("#notify-list").removeClass('hidden').DataTable({
                    "ordering": true,
                    "dom": '<"listtable"fit>pl',
                    "responsive": true,
                    "order": [
                        [
                            4, "desc"
                        ]
                    ],
                    "oLanguage": {
                        "sEmptyTable": "Данные об атаках не найдены.",
                        "sInfo": "Показано с _START_ по _END_ из _TOTAL_",
                        "sInfoEmpty": "Показано с 0 по 0 из 0",
                        "sInfoFiltered": "(отфильтровано из _MAX_ записей)",
                        "sInfoPostFix": "",
                        "sInfoThousands": ",",
                        "sLengthMenu": "Показать _MENU_ записей",
                        "sLoadingRecords": "Загрузка...",
                        "sProcessing": "Обработка...",
                        "sSearch": "",
                        "sZeroRecords": "Данные об атаках не найдены.",
                        "oPaginate": {
                            "sFirst": "Первая",
                            "sLast": "Последняя",
                            "sNext": "Вперед",
                            "sPrevious": "Назад"
                        }
                    },
                    "pageLength": 100,
                    "processing": true,
                    "serverSide": true,
                    "bPaginate": true,
                    "deferRender": true,
                    "ajax": {
                        "url": "/?m=NotificationsDDoSAttacks",
                        "type": "POST",
                        "dataSrc": 'data',
                        "data": function (d) {
                            d.action = 'get_client_notify';
                            d.user_id = window.notifications_ddos_attacks.userid;
                            d.client_type = "user";
                            d.sign = window.notifications_ddos_attacks.sign;
                        }
                    },
                    "columns": [
                        {"data": "ip"},
                        {"data": "proto"},
                        {"data": "bits", "bSortable": false},
                        {"data": "packets", "bSortable": false},
                        {"data": "start"},
                        {"data": "end"}
                    ],
                    "lengthMenu": [
                        [50, 100, 500, 1000],
                        [50, 100, 500, 1000]
                    ],
                    "stateSave": true
                });
                jQuery('#tableLoading').addClass('hidden');
                $("#client_setting").async({
                    success: function (response) {
                        $.notify("Изменения сохранены", "success");
                    },
                    error: function (request) {
                        $.notify("Изменения не сохранены", "error");
                    }
                });
            });
        }
    );
    {/literal}
</script>
<form id="client_setting" action="/?m=NotificationsDDoSAttacks">
    <div class="form-check" style="margin-bottom: 4px">
        <input id="notifyStatus" type="checkbox" data-toggle="toggle" name="client_email_notify_status"
               data-on="Email уведомления включены" data-off="Email уведомления отключены" data-width="250"
               data-size="mini">
    </div>
</form>
<div class="table-container clearfix">
    <div class="text-center" id="tableLoading">
        <p><i class="fas fa-spinner fa-spin"></i> {$LANG.loading}</p>
    </div>
    <table id="notify-list" class="table table-list">
        <thead>
        <tr>
            <th>
                IP адрес
            </th>
            <th>
                Тип
            </th>
            <th>
                Пик в bps
            </th>
            <th>
                Пик в pps
            </th>
            <th>
                Начало атаки
            </th>
            <th>
                Конец атаки
            </th>
        </tr>
        </thead>
    </table>
</div>