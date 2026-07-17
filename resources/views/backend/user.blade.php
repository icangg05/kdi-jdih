<x-layouts.backend title="User" :listNav="[['label' => 'User']]">

	<x-backend.table-data title="User" :data="$data" prefixRoute="user" :columns="[
	    ['title' => 'Username', 'key' => 'username'],
	    ['title' => 'Email', 'key' => 'email', 'format' => 'href'],
	]" />

</x-layouts.backend>
