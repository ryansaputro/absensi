export default {
  methods: {
    $can(permissionName) {
      return Permissions.indexOf(permissionName) !== -1;
    },
    
  },
};
