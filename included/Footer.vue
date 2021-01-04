<template>
  <nb-footer-tab v-if="userType === 'client'">
    <nb-button :active="activeBtn == 'home'" :on-press="() => goToPage('Home')">
      <nb-icon :active="activeBtn == 'home'" name="home" />
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
    <nb-button :style="{ padding: 0 }" :active="activeBtn == 'docs'" :on-press="() => goToPage('Documents')">
      <nb-icon :active="activeBtn == 'docs'" name="folder-open" />
      <nb-text >{{ $root.lang.t('file') }}</nb-text>
    </nb-button>
  </nb-footer-tab>
    <nb-footer-tab v-else>
    <nb-button :active="activeBtn == 'home'" :on-press="() => goToPage('Home')">
      <nb-icon :active="activeBtn == 'home'" name="home" />
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
import { AsyncStorage } from "react-native";
  export default {
    props: {
      activeBtn: {
        type: String
      },
      userType: '',
    },
    data() {
      return {};
    },
      mounted() {
    this.getUser().then(val => {
      // got value here
      this.userType = val.type;
    }).catch(e => {
      // error
      console.log(e);
    });
  },
    methods: {
      goToPage: function (page) {
        this.$parent.navigation.navigate(page);
      },
      getUser: async function () {
        let value = '';
        value = await AsyncStorage.getItem('login');
        value = JSON.parse(value);
        return value;
    },
    }
  }
</script>