<script>
    window.NotificationsDDoSAttacks = {
        userid: {$userid},
        sign: "{$sign}",
        admin_path: "{$customadminpath}",
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
                            d.action = 'get_notify';
                            d.user_id = window.NotificationsDDoSAttacks.userid;
                            d.client_type = "admin";
                            d.sign = window.NotificationsDDoSAttacks.sign;
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
                    "columnDefs": [{
                        "targets": [6],
                        "data": null,
                        "render": function (data, type, row, meta) {
                            return "<a href=\"addonmodules.php?module=NotificationsDDoSAttacks&action=delete_email&id="+data.id+"\" onclick=\"return window.confirm('Вы точно хотите удалить запись об атаке на ip " + data.ip + " ?');\"\n" +
                                "                           title=\"Нажмите для удаления\">\n" +
                                "                            <img src=\"/" + window.NotificationsDDoSAttacks.admin_path + "/images/delete.gif\" border=\"0\" alt=\"удалить\">\n" +
                                "                        </a>";
                        }
                    }],
                    "lengthMenu": [
                        [50, 100, 500, 1000, 2000, 3000, 5000],
                        [50, 100, 500, 1000, 2000, 3000, 5000]
                    ],
                    "stateSave": true
                });
                jQuery('#tableLoading').addClass('hidden');
            });
        }
    );
    {/literal}
</script>
<style>
    div#notify-list_length {
        float: left;
    }

    div#notify-list_paginate {
        float: right;
    }

    div#notify-list_filter {
        float: right;
    }

    div#notify-list_info {
        float: left;
    }
</style>

<div class="col-md-12">
    <div id="tableBackground" class="tablebg">
        <div class="text-center" id="tableLoading">
            <p><i class="fas fa-spinner fa-spin"></i> {$LANG.loading}</p>
        </div>
        <table id="notify-list" width="100%" class="datatable no-margin hidden ">
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
                <th style="width: 2%;"></th>
            </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>
