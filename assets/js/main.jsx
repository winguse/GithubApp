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
/***	"user_major": function(){
		var User_MajorInfo = React.createClass({
			displayName: 'User_MajorInfo',
			render: function(){
				var totalFeildsCount = this.props.fields.length;
				return (
					<tr>
						{
							this.props.fields.map(function(field, idx){
								var userId= this.id;
								return (<td key={idx}>{this[field]}</td>);
							}.bind(this.props.user))
						}
					</tr>
				);
			}
		});
		var User_MajorTable = React.createClass({
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
			displayName: 'User_MajorTable',
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
			<User_MajorTable 
				users={users}
				fields={['id','name']
				fieldDisplayNames={['Id','Major']}
			/>,
			document.getElementById('content')
		);
	},
	****/
	"admin_users": function(){
		var UserInfo = React.createClass({
			displayName: 'UserInfo',
			render: function(){
				var totalFeildsCount = this.props.fields.length;
				return (
					<tr>//可以将real_name单独用<a>标签？？
						{
							this.props.fields.map(function(field, idx){
								var userId= this.id;//this.id从哪儿来？？
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
								if(idx === totalFeildsCount - 1)
									return (
										<td key={idx}>
											<IconButton name="Edit" icon="edit" size="xs" onClick={editUser}/>
											<IconButton name="Delete" icon="trash" size="xs" onClick={deleteUser}/>
										</td>
									);
								//if(idx===0) return (<a key={idx}>{this[field]}</a>);
								return (<td key={idx}>{this[field]}</td>);
							}.bind(this.props.user))//只知道字面意思，比知道实际运行情况。
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
								{this.props.fieldDisplayNames.map(function(displayName, idx){//idx默认自动增长？？
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
		// 添加成功后要给用户建一个repository
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
		$form.ajaxForm({//怎么触发的？profile中的profile-form表单提交时
			success: function(d) {//function.php中的JQuery ajax之间的数据交互？d是怎么传进来的？？？
				$message.hide();//表单成功提交后调用的回调函数。如果提供“success”回调函数，当从服务器返回响应后它被调用。然后由dataType选项值决定传回responseText还是responseXML的值。
				if(d.code !== 0){
					$message.attr('class', 'alert alert-danger');
					$message.text(d.message);
					$message.fadeIn();
					form[d.field].focus();
				}else{
					$message.attr('class', 'alert alert-success');
					//跳转到提交成功页面
					//window.location="<?=$url?>"
					$message.text('Your profile is updated succssfully.');//update成功之后没有alert出text？？？
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
					url: GITHUB_APP_BASE_PATH + '/api/majors/',//指定提交表单数据的URL。默认值：表单的action属性值
					data: {//发送到服务器的数据;
						name: $name[0].value//映射类型数据
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