<?php if (isset($this->revisions) && count($this->revisions)) { ?>
    <form method="post" action="" class="nomargin" id="revisions">
        <div class="log"></div>
        <div id="logModal" class="modal hide fade" tabindex="-1">
            <div class="modal-header"></div>
            <div class="modal-body"></div>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo __('OK');?></button>
            </div>
        </div>

        <table class="table table-condensed table-striped table-bordered">
            <thead>
                <tr>
                    <th style="width: 13px;"><input type="checkbox" style="margin-top: 0;" /></th>
                    <th><?php echo __('Revision ID'); ?></th>
                    <th><?php echo __('Status'); ?></th>
                    <th><?php echo __('Modify Time'); ?></th>
                    <th><?php echo __('Lastly Run Time'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($this->revisions as $revision) { ?>
                    <?php
                        $ran = $this->revision >= $revision;
                        $class = array();
                        if ($ran) {
                            $class[] = 'ran';
                        }

                        $files = $this->_getRevisionFiles($revision);
                        $info = (new \DBV\Revisions\RunStatus($revision))->get() ?: [];
                    ?>
                    <tr data-revision="<?php echo $revision; ?>"<?php echo count($class) ? ' class="' . implode(' ', $class) . '"'  : ''; ?>>
                        <td class="center">
                            <input type="checkbox" name="revisions[]" value="<?php echo $revision; ?>"<?php echo $ran ? '' : ' checked="checked"'; ?> style="margin-top: 7px;" />
                        </td>
                        <td><a href="javascript:" class="revision-handle"><?php echo $revision; ?></a></td>
                        <td>
                            <?php if ($info['status']):?>
                            <span class="label label-success">ACTIVE</span>
                            <?php else:?>
                            <span class="label label-important">INACTIVE</span>
                            <?php endif;?>
                        </td>
                        <td>
                            <?php if (!$info['modify_time']):?>
                                -
                            <?php else:?>
                                <?php if ($info['lastly_run_time'] && $info['lastly_run_time'] < $info['modify_time']):?>
                                    <?php $tmp = 'text-warning';?>
                                <?php else:?>
                                    <?php $tmp = 'text-info';?>
                                <?php endif;?>
                                <span class="<?php echo $tmp;?>"><?php echo date('Y-m-d H:i:s', $info['modify_time']);?></span>
                            <?php endif;?>
                        </td>
                        <td><?php echo $info['lastly_run_time'] ? date('Y-m-d H:i:s', $info['lastly_run_time']) : '-';?></td>
                    </tr>
                    <tr class="revision-files hidden">
                        <td colspan="4">
                            <?php if (count($files)) { ?>
                                <div>
                                    <?php $i = 0; ?>
                                    <?php foreach ($files as $file) { ?>
                                        <?php
                                            $extension = pathinfo($file, PATHINFO_EXTENSION);
                                            $content = htmlentities($this->_getRevisionFileContents($revision, $file), ENT_QUOTES, 'UTF-8');
                                            $lines = substr_count($content, "\n");
                                        ?>
                                        <div id="revision-file-<?php echo $revision; ?>-<?php echo ++$i; ?>">
                                            <div class="log"></div>
                                            <div class="alert alert-info heading">
                                                <button data-role="editor-save" data-revision="<?php echo $revision; ?>" data-file="<?php echo $file; ?>" type="button" class="btn btn-mini btn-info pull-right" style="margin-top: -1px;"><?php echo __('Save file') ?></button>
                                                <strong class="alert-heading"><?php echo $file; ?></strong>
                                            </div>
                                            <textarea data-role="editor" name="revision_files[<?php echo $revision; ?>][<?php echo $file; ?>]" rows="<?php echo $lines + 1; ?>"><?php echo $content; ?></textarea>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <input type="submit" class="btn btn-primary" value="<?php echo __('Run selected revisions');?>" />
        <input type="button" data-action="refresh" data-loading-text="<?php echo __('Refreshing');?>" class="btn btn-info" value="<?php echo __('Refresh selected modify time');?>" />
    </form>
<?php } else { ?>
    <div class="alert alert-info nomargin">
        <?php echo __('No revisions in #{path}', array('path' => '<strong>' . DBV_REVISIONS_PATH . '</strong>')) ?>
    </div>
<?php } ?>
<script type="text/javascript">
    document.observe('dom:loaded', function () {
        var form = $('revisions');
        if (!form) {
            return;
        }

        var textareas = form.select('textarea');
        textareas.each(function (textarea) {
            textarea['data-editor'] = CodeMirror.fromTextArea(textarea, {
                mode: 'text/x-sql',
                tabMode: 'indent',
                matchBrackets: true,
                autoClearEmptyLines: true,
                lineNumbers: true,
                theme: 'default'
            });
        });

        $$('.revision-handle').invoke('observe', 'click', function (event) {
            var element = event.findElement('.revision-handle');
            var revision = element.up('tr');
            var container = revision.next('.revision-files');
            if (container) {
                var j$container = j$(container);
                if (j$container.hasClass('hidden')) {
                    j$container.removeClass('hidden');
                    j$(revision)
                        .children('td:first')
                        .attr('rowspan', '2');
                } else {
                    j$container.addClass('hidden');
                    j$(revision)
                        .children('td:first')
                        .removeAttr('rowspan');
                }

                if (!container.visible()) {
                    return;
                }

                var textareas = container.select('textarea[data-role="editor"]');
                if (textareas) {
                    textareas.each(function (textarea) {
                        var j$textarea = j$(textarea);
                        j$textarea.next('div.CodeMirror').css('width', j$textarea.parent('div[id^="revision-file"]').width()+ 'px');
                        textarea['data-editor'].refresh();
                    });
                }
            }
        });

        $$('button[data-role="editor-save"]').invoke('observe', 'click', function (event) {
            var self = this;

            var editor = this.up('.heading').next('textarea')['data-editor'];
            var container = this.up('[id^="revision-file"]');

            this.disable();

            clear_messages(container);

            new Ajax.Request('index.php?a=saveRevisionFile', {
                parameters: {
                    revision: this.getAttribute('data-revision'),
                    file: this.getAttribute('data-file'),
                    content: editor.getValue()
                },
                onSuccess: function (transport) {
                    self.enable();

                    var response = transport.responseText.evalJSON();

                    if (response.error) {
                        return render_messages('error', container, response.error);
                    }

                    render_messages('success', container, response.message);
                }
            });
        });

        var alert_messages = function(type, messages, heading) {
            // 标题
            var header = '';
            if (type === 'error') {
                header += '<h3 class="label label-important">' + heading + '</h3>';
            } else if (type === 'success') {
                header += '<h3 class="label label-success">' + heading + '</h3>';
            }

            // 内容
            if (!(messages instanceof Array)) messages = [messages];

            var content = '<ul>';
            for (var i = 0; i < messages.length; i++) {
                content += '<li>' + messages[i] + '</li>';
            }
            content += '</ul>';

            j$('#logModal')
                .children('.modal-header').html(header)
                .end().children('.modal-body').html(content)
                .end().modal({
                    backdrop: 'static'
                });
        };

        form.on('submit', function (event) {
            event.stop();

            var data = form.serialize(true);

            clear_messages(this);

            if (!data.hasOwnProperty('revisions[]')) {
                render_messages('error', this, "<?php echo __("You didn't select any revisions to run") ?>");
                Effect.ScrollTo('log', {duration: 0.2});
                return false;
            }

            form.disable();

            new Ajax.Request('index.php?a=revisions', {
                parameters: {
                    "revisions[]": data['revisions[]']
                },
                onSuccess: function (transport) {
                    form.enable();

                    var response = transport.responseText.evalJSON();

                    if (typeof response.error != 'undefined') {
                        return APP.growler.error('<?php echo __('Error!'); ?>', response.error);
                    }

                    if (response.messages.error) {
                        alert_messages('error', response.messages.error, '<?php echo __('The following errors occured:'); ?>');
                    }

                    if (response.messages.success) {
                        alert_messages('success', response.messages.success, '<?php echo __('The following actions completed successfuly:'); ?>');
                    }

                    var revision = parseInt(response.revision);
                    if (!isNaN(revision)) {
                        var rows = form.select('tr[data-revision]');

                        rows.each(function (row) {
                            row.removeClassName('ran');
                            if (row.getAttribute('data-revision') > revision) {
                                return;
                            }
                            row.addClassName('ran');
                            row.down('.revision-files').hide();
                            row.down('input[type="checkbox"]').checked = false;
                        });
                    }

                    Effect.ScrollTo('log', {duration: 0.2});
                }
            });
        });

        var j$form = j$('#revisions');
        j$form.find(':button[data-action="refresh"]').on('click', function() {
            var j$button = j$(this);
            var buttonText = j$button.val();
            var loadingText = j$button.data('loading-text');
            j$button.val(loadingText)
                .addClass('disabled')
                .prop('disabled', true);

            // Button 加载效果
            var count = 0;// 计点器
            var handler = setInterval(function() {
                count ++;
                if (count > 3) count = 0;
                var str = '';
                for(var i = 0;i < count;i ++) {
                    str += '.';
                }
                j$button.val(loadingText + str);
            }, 1000);

            j$.ajax({
                type: 'POST',
                url: 'index.php?a=refresh',
                data: j$form.find(':input[name="revisions[]"]').serializeArray(),
                success: function(res) {
                    // 还原 Button 效果
                    clearInterval(handler);
                    j$button
                        .prop('disabled', false)
                        .removeClass('disabled')
                        .val(buttonText);

                    if (res.error) {
                        alert_messages('error', res.error, '<?php echo __('Errors occured:');?>');
                    } else {
                        alert_messages('success', res.message, '<?php echo __('Successfuly:');?>');
                    }
                }
            });
            return false;
        });
    });
</script>
