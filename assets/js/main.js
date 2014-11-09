var GITHUB_APP_BASE_PATH = '/app/github';

var github_app = {
	"major": function() {
		var $save = $("#major-editor-save");
		var $modal = $("#major-editor");
		var $id_group = $("#major-id-group");
		var $alert = $("#major-editor-error");
		var $modal_label = $("#major-editor-label");
		var $id = $("#major-id");
		var $name = $("#major-name");
		$('#major-add').click(function() {
			var $name = $("#major-name");
			$id_group.hide();
			$name[0].value = "";
			$modal_label.text('Add Major Information');
			$modal.modal();
			$save.click(function() {
				$.ajax({
					type: "PUT",
					url: GITHUB_APP_BASE_PATH + '/api/majors/',
					data: {
						name: $name[0].value
					},
					success: function(d) {
						if (d.code === 0) {
							$modal.modal('hide');
							window.location.reload();
						} else {
							$alert.text('Update failed, please try again or contract the server administrator.');
							$alert.fadeIn();
						}
					}
				});
			});
		});
		$('.major-edit').click(function() {
			var $this = $(this);
			var $tr = $this.parent().parent();
			var id = parseInt($tr.attr('data-id'));
			var name = $tr.attr('data-name');
			$id[0].value = id;
			$name[0].value = name;
			var $modal = $("#major-editor");
			$modal_label.text('Editing Major Information');
			$modal.modal();
			$save.click(function() {
				$.post(GITHUB_APP_BASE_PATH + '/api/majors/' + id, {
						name: $name[0].value
					},
					function(d) {
						if (d.code === 0) {
							$tr.attr('data-name', $name[0].value);
							$tr.find(">:nth-child(2)").text($name[0].value);
							$modal.modal('hide');
						} else {
							$alert.text('Update failed, please try again or contract the server administrator.');
							$alert.fadeIn();
						}
					});
			});
		});
		$(".major-delete").click(function() {
			var $this = $(this);
			var $tr = $this.parent().parent();
			if ($this.hasClass('btn-danger')) {
				$.ajax({
					url: GITHUB_APP_BASE_PATH + '/api/majors/' + parseInt($tr.attr('data-id')),
					type: "DELETE",
					success: function(d) {
						if (d.code === 0)
							$tr.remove();
						else
							alert('delete failed..');
					}
				});
			} else {
				$this.addClass('btn-danger');
				$this.find('sr-only').text('Confirm Delete?');
				setTimeout(function() {
					$this.removeClass('btn-danger');
					$this.find('sr-only').text('Delete');
				}, 5000);
			}
		});

		$modal.on('show.bs.modal', function() {
			$alert.hide();
		});
		$modal.on('hidden.bs.modal', function() {
			$save.unbind('click');
			$id_group.show();
		});
	}
};



if (page) {
	github_app[page]();
}