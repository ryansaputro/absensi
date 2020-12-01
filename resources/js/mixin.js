export default {
  methods: {
    $can(permissionName) {
      if (typeof (localStorage.getItem('user')) !== 'undefined') {
        window.Permissions = localStorage.getItem('user');
      } else {
        window.Permissions = [];
      }
      return Permissions.indexOf(permissionName) !== -1;
    },
    
  },
};
