String.prototype.htmlEncode = function() {
	return this.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
};

var GITHUB_APP_BASE_PATH = '/app/github';


var IconButton = React.createClass({
	displayName: "IconButton",
	render: function(){
		var iconClass = "glyphicon glyphicon-" + this.props.icon;
		var btnClass = "btn btn-" + this.props.size;
		return (
			<button type="button" className={btnClass} title={this.props.name} onClick={this.props.onClick}>
				<span className={iconClass}></span>
				<span className="sr-only">{this.props.name}</span>
			</button>
		);
	}
});



var github_app = {
	"admin_users": function(){
		var UserInfo = React.createClass({
			displayName: 'UserInfo',
			render: function(){
				var totalFeildsCount = this.props.fields.length;
				return (
					<tr>
						{
							this.props.fields.map(function(field, idx){
								var userId= this.id;
								var editUser = function(){
									console.log("edit user" + userId);
									window.location = GITHUB_APP_BASE_PATH + '/user/' + userId;
								};
								var deleteUser = function(){
									if(confirm('Are you confirm to delete user id = ' + userId)){
										$.ajax({
											url: GITHUB_APP_BASE_PATH + '/api/user/' + userId,
											type: "DELETE",
											success: function(d) {
												if (d.code === 0)
													window.location.reload();
												else
													alert('delete failed..');
											}
										});
									}
								};
								var hrefUser = function(){
									console.log("href User "+userId);
									window.location = GITHUB_APP_BASE_PATH + '/repos/content/'+ userId;/**应该跳转到创建用户的界面**/
								};
								if(idx === totalFeildsCount - 1)
									return (
										<td key={idx}>
											<IconButton name="Edit" icon="edit" size="xs" onClick={editUser}/>
											<IconButton name="Delete" icon="trash" size="xs" onClick={deleteUser}/>
										</td>
									);
								if(idx===1) {
									return (<td key={idx}><a title={this[field]}  onClick={hrefUser}>{this[field]}</a></td>);
								}else return (<td key={idx}>{this[field]}</td>);
							}.bind(this.props.user))
						}
					</tr>
				);
			}
		});
		var UsersTable = React.createClass({
			getInitialState: function(){
				return {desc: false, field: false}
			},
			headerClick: function(e){
				var field = this.props.fields[e.target.cellIndex];
				var desc = (field === this.state.field) && !this.state.desc;
				this.state.field = field;
				this.state.desc = desc;
				this.setProps({users: users.sort(function(a, b){
					return desc ^ (a[field] > b[field]);
				})});
			},
			displayName: 'UsersTable',
			render: function() {
				return (
					<table className="table">
						<thead>
							<tr onClick={this.headerClick}>
								{this.props.fieldDisplayNames.map(function(displayName, idx){
									return <th key={idx}>{displayName}</th>
								})}
							</tr>
						</thead>
						<tbody>
							{this.props.users.map(function(user){
								return <UserInfo key={user.id} user={user} fields={this.props.fields}/>
							}.bind(this))}
						</tbody>
					</table>
				); 
			}
		});
		React.render(
			<UsersTable 
				users={users}
				fields={['id', 'github_login', 'role', 'real_name', 'grade', 'student_id', 'major_id', 'email', 'role']}
				fieldDisplayNames={['Id', 'GitHub Info', 'Role', 'Name', 'Grade', 'Student Id', 'Major', 'Email', 'Action']}
			/>,
			document.getElementById('content')
		);
	},
	"profile": function() {
		var $message = $("#message");
		$message.hide();
		var majorOptionsHtml = '<option value="null">Please set your major</option>';
		for (var idx in majors) {
			var major_id = parseInt(majors[idx].id);
			var major_name = majors[idx].name;
			majorOptionsHtml += '<option value="' + major_id + '">' + major_name.htmlEncode() + '</option>';
		}
		$("#inputMajor").html(majorOptionsHtml);
		var $form = $("#profile-form");
		var form = $form[0];
		for (var key in userInfo) {
			form[key].value = userInfo[key];
		}
		if (parseInt(form.student_id.value) === 0) {
			form.student_id.value = '';
		}
		$form.ajaxForm({
			success: function(d) {
				$message.hide();
				if(d.code !== 0){
					$message.attr('class', 'alert alert-danger');
					$message.text(d.message);
					$message.fadeIn();
					form[d.field].focus();
				}else{
					$message.attr('class', 'alert alert-success');
					
					$message.text('Your profile is updated succssfully.');
					$message.fadeIn();
				}
			}
		});
	},
	"admin_majors": function() {
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



if (page && github_app[page]) {
	github_app[page]();
}