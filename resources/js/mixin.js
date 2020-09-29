export default {
  methods: {
    $can(permissionName) {
      // console.log(localStorage.getItem('role'))
      if (typeof (localStorage.getItem('user')) !== 'undefined') {
        window.Permissions = localStorage.getItem('user');
      } else {
        window.Permissions = [];
      }
      // console.log(window.Permissions)
      return Permissions.indexOf(permissionName) !== -1;
    },
    
  },
};
