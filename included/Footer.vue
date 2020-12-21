<template>
  <nb-footer-tab>
    <nb-button :on-press="() => goToPage('Home')">
      <nb-icon name="home" />
      <nb-text>{{ $root.lang.t('home') }}</nb-text>
    </nb-button>
    <nb-button :active="activeBtn == 'account'" :on-press="() => goToPage('Account')">
      <nb-icon :active="activeBtn == 'account'" name="person" />
      <nb-text>{{ $root.lang.t('my_account') }}</nb-text>
    </nb-button>
    <nb-button :active="activeBtn == 'appointments'" :on-press="() => goToPage('Appointments')">
      <nb-icon :active="activeBtn == 'appointments'" name="calendar" />
      <nb-text>{{ $root.lang.t('calendar') }}</nb-text>
    </nb-button>
    <nb-button :active="activeBtn == 'docs'" :on-press="() => goToPage('Documents')">
      <nb-icon :active="activeBtn == 'docs'" name="laptop" />
      <nb-text>{{ $root.lang.t('file') }}</nb-text>
    </nb-button>
  </nb-footer-tab>
</template>

<script>
  export default {
    props: {
      activeBtn: {
        type: String
      }
    },
    data() {
      return {};
    },
    methods: {
      goToPage: function (page) {
        this.$parent.navigation.navigate(page);
      }
    }
  }
</script>