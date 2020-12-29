<template>
  <nb-footer-tab>
    <nb-button class="button" :on-press="() => goToPage('Consultant')">
      <nb-icon name="home" />
      <nb-text>{{ $root.lang.t('home') }}</nb-text>
    </nb-button>
    <nb-button :active="activeBtn == 'clients'" :on-press="() => goToPage('Clients')">
      <nb-icon :active="activeBtn == 'clients'" name="people" />
      <nb-text>{{ $root.lang.t('clients') }}</nb-text>
    </nb-button>
    <nb-button class="button" :active="activeBtn == 'appointments'" :on-press="() => goToPage('AppointmentsConsultant')">
      <nb-icon :active="activeBtn == 'appointments'" name="calendar" />
      <nb-text>{{ $root.lang.t('calendar') }}</nb-text>
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