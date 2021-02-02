<template>
  <nb-footer-tab v-if="userType === 'client'">
	<nb-button :style="activeBtn !== 'home' ?  styles.button : ''"  :active="activeBtn == 'home'" :on-press="() => goToPage('Home')">
	  <nb-icon :style="activeBtn !== 'home' ?  styles.icon : ''"  :active="activeBtn == 'home'" name="home" />
	  <nb-text :style="activeBtn !== 'home' ?  styles.icon : ''" >{{ $root.lang.t('home') }}</nb-text>
	</nb-button>
	<nb-button :style="activeBtn !== 'account' ?  styles.button : ''"  :active="activeBtn == 'account'" :on-press="() => goToPage('Account')">
	  <nb-icon :style="activeBtn !== 'account' ?  styles.icon : ''" :active="activeBtn == 'account'" name="person" />
	  <nb-text :style="activeBtn !== 'account' ?  styles.icon : ''">{{ $root.lang.t('my_account') }}</nb-text>
	</nb-button>
	<nb-button :style="activeBtn !== 'appointments' ?  styles.button : ''" :active="activeBtn == 'appointments'" :on-press="() => goToPage('Appointments')">
	  <nb-icon :style="activeBtn !== 'appointments' ?  styles.icon : ''" :active="activeBtn == 'appointments'" name="calendar" />
	  <nb-text :style="activeBtn !== 'appointments' ?  styles.icon : ''">calendar</nb-text>
	</nb-button>
	<nb-button  :style="activeBtn !== 'docs' ?  styles.button : ''" :active="activeBtn == 'docs'" :on-press="() => goToPage('Documents')">
	  <nb-icon :style="activeBtn !== 'docs' ?  styles.icon : ''" :active="activeBtn == 'docs'" name="folder-open" />
	  <nb-text :style="activeBtn !== 'docs' ?  styles.icon : ''">{{ $root.lang.t('file') }}</nb-text>
	</nb-button>
  </nb-footer-tab>
	<nb-footer-tab v-else>
	<nb-button :style="activeBtn !== 'home' ?  styles.button : ''"  :active="activeBtn == 'home'" :on-press="() => goToPage('Home')">
	  <nb-icon :style="activeBtn !== 'home' ?  styles.icon : ''"  :active="activeBtn == 'home'" name="home" />
	  <nb-text :style="activeBtn !== 'home' ?  styles.icon : ''" >{{ $root.lang.t('home') }}</nb-text>
	</nb-button>
	<nb-button :style="activeBtn !== 'clients' ?  styles.button : ''" :active="activeBtn == 'clients'" :on-press="() => goToPage('Clients')">
	  <nb-icon :style="activeBtn !== 'clients' ?  styles.icon : ''"  :active="activeBtn == 'clients'" name="people" />
	  <nb-text :style="activeBtn !== 'clients' ?  styles.icon : ''" >{{ $root.lang.t('clients') }}</nb-text>
	</nb-button>
	<nb-button :style="activeBtn !== 'appointments' ?  styles.button : ''" :active="activeBtn == 'appointments'" :on-press="() => goToPage('AppointmentsConsultant')">
	  <nb-icon :style="activeBtn !== 'appointments' ?  styles.icon : ''" :active="activeBtn == 'appointments'" name="calendar" />
	  <nb-text :style="activeBtn !== 'appointments' ?  styles.icon : ''">calendar</nb-text>
	</nb-button>
  </nb-footer-tab>
</template>

<script>
import { AsyncStorage } from "react-native";
import {styles} from '../pages/styling/style';

  export default {
	props: {
	  activeBtn: {
		type: String
	  },
	  userType: '',
	},
	data() {
	  return {
		  styles
	  };
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