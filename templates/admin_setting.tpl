<script>
    window.notifications_ddos_attacks = {
        userid: {$userid},
        client_type: "admin",
        sign: "{$sign}"
    };
    $(function () {
        $("#admin_setting").async({
            success: function (response) {
                $.notify("Изменения сохранены", "success");
            },
            error: function (request) {
                $.notify("Изменения не сохранены", "error");
            }
        });
    });
</script>
<div class="col-md-10">
    <form id="admin_setting" action="/?m=NotificationsDDoSAttacks">
        <div class="form-group row" style="padding-top: 15px">
            <div class="col-md-10">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="ddos_guard_parsing_status"
                           id="ddos_guard_parsing_status"
                            {if 'ddos_guard_parsing_status'|array_key_exists:$setting and $setting.ddos_guard_parsing_status eq "1"}
                                checked
                            {/if}
                    >
                    <label class="form-check-label" for="ddos_guard_parsing_status">
                        Включить парсинг писем которые точно соответствуют теме: DDoS Attack
                    </label>
                </div>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-md-10">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="wanguard_parsing_status"
                           id="wanguard_parsing_status"
                            {if 'wanguard_parsing_status'|array_key_exists:$setting and $setting.wanguard_parsing_status eq "1"}
                                checked
                            {/if}
                    >
                    <label class="form-check-label" for="wanguard_parsing_status">
                        Включить парсинг писем тема которых соответствует регулярному выражению: /^\[Anomaly
                        #\d+].+stopped.+$/
                    </label>
                </div>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-md-5">
                <label for="clientNotifyEmailTemplate">Шаблон email уведомлений клиента</label>
                <select class="form-control " id="clientNotifyEmailTemplate"
                        name="clientNotifyEmailTemplate">
                    {if  not 'clientNotifyEmailTemplate'|array_key_exists:$setting}
                        <option value="0" disabled selected>Не выбрано</option>
                    {/if}
                    {html_options options=$template selected=$setting.clientNotifyEmailTemplate}
                </select>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-md-5">
                <label for="imap_server_ip">Ip/Домен imap сервера</label>
                <input class="form-control" id="imap_server_ip" name="imap_server_ip" type="text"
                        {if 'imap_server_ip'|array_key_exists:$setting} value="{$setting.imap_server_ip}" {/if}
                >
            </div>
        </div>
        <div class="form-group row">
            <div class="col-md-5">
                <label for="imap_server_port">Port imap сервера с ssl</label>
                <input class="form-control" id="imap_server_port" name="imap_server_port" type="text"
                        {if 'imap_server_port'|array_key_exists:$setting} value="{$setting.imap_server_port}" {/if}
                >
            </div>
        </div>
        <div class="form-group row">
            <div class="col-md-5">
                <label for="imap_server_login">Логин</label>
                <input class="form-control" name="imap_server_login" id="imap_server_login" type="text"
                        {if 'imap_server_login'|array_key_exists:$setting} value="{$setting.imap_server_login}" {/if}
                >
            </div>
        </div>
        <div class="form-group row">
            <div class="col-md-5">
                <label for="imap_server_password">Пароль</label>
                <input class="form-control" name="imap_server_password" id="imap_server_password" type="text"
                        {if 'imap_server_password'|array_key_exists:$setting} value="{$setting.imap_server_password}" {/if}
                >
            </div>
        </div>
        <div class="form-group row">
            <div class="col-md-5">
                <label for="allow_save_attack">Сохранять атаки для подсетей:</label>
                <textarea class="form-control mx-sm-3" id="allow_save_attack" name="allow_save_attack" rows="7"
                          cols="10"
                          placeholder="Поддерживаемые форматы записи: 255.255.*.* или 1.2.3.0-1.2.3.255 или 127.0.0.0/24 каждый диапазон должен быть с новой строки">{if 'allow_save_attack'|array_key_exists:$setting}{$setting.allow_save_attack}{/if}</textarea>
            </div>
        </div>
    </form>
</div>
