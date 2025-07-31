<div class="page-header">
	<div>
		<h2 class="main-content-title tx-24 mg-b-5">Master Module</h2>
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="javascript:void(0);">Developer</a></li>
			<li class="breadcrumb-item active" aria-current="page"><a href="javascript:void(0);">Module</a></li>
		</ol>
	</div>
</div>

<?php 
Allmedia\Shared\AdminPermission\SharedViews::render("permission-module/view", [
	'isAllowToCreate' => $adminPermissionCore->isHavePermission($moduleId, "create"),
	'isAllowToUpdate' => $adminPermissionCore->isHavePermission($moduleId, "update"),
	'isAllowToDelete' => $adminPermissionCore->isHavePermission($moduleId, "delete"),
	'availableGroups' => $adminPermissionCore->availableGroup(),
]); 
?>