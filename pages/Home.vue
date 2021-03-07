<template>
  <nb-container>
      <nb-header :style="{elevation:0,backgroundColor: '#FFF',
          shadowOpacity: 0,
          borderBottomWidth: 0,}">
        <nb-right :style="{flex:1}">
          <nb-button transparent :on-press="logout">
            <nb-icon :style="{ color: '#0078ae',fontSize:32 }" :name=" Platform.OS === 'android' ? 'exit' : 'log-out'" />
          </nb-button>
        </nb-right>
      </nb-header>
    <view v-if="$root.user.type === 'client'" :style="{ flex: 1,  justifyContent: 'center', alignItems: 'center', zIndex:-1,padding: 20,marginTop:-70 }">
          <nb-text :style="{ padding: 10, fontSize: 22 }">{{ $root.lang.t('welcome') }} {{$root.user.name}}</nb-text>
        <nb-button full info :style="styles.btn" :on-press="() => goToPage('Account')">
          <nb-text :style="styles.btnText">{{ $root.lang.t('my_account') }}</nb-text>
        </nb-button>
        <nb-button full info :style="styles.btn" :on-press="() => goToPage('Appointments')">
          <nb-text :style="styles.btnText">{{ $root.lang.t('appointments') }}</nb-text>
        </nb-button>
        <nb-button full info :style="styles.btn" :on-press="() => goToPage('Documents')">
          <nb-text :style="styles.btnText">{{ $root.lang.t('check_documents') }}</nb-text>
        </nb-button>
    </view>
    <view v-else :style="{ flex: 1,  justifyContent: 'center', alignItems: 'center', padding: 20,zIndex:-1, marginTop:-110  }">
      <nb-text :style="{ padding: 10, fontSize: 22 }">{{ $root.lang.t('welcome') }} {{$root.user.name}}</nb-text>
        <nb-button full info :style="styles.btn" :on-press="() => goToPage('Clients')">
          <nb-text :style="styles.btnText">{{ $root.lang.t('clients') }}</nb-text>
        </nb-button> 
        <nb-button full info :style="styles.btn" :on-press="() => goToPage('AppointmentsConsultant')">
          <nb-text :style="styles.btnText">{{ $root.lang.t('appointments') }}</nb-text>
        </nb-button>
    </view>
    <view :style="styles.center">
      <image :source="require('../assets/images/logo.png')" />
    </view>
      <nb-footer>
      <footer-nav
        :style="styles.background"
        activeBtn="home"
      ></footer-nav>
    </nb-footer>
  </nb-container>
</template>
<script>

import { Platform } from "react-native";
import AsyncStorage from '@react-native-async-storage/async-storage';
import FooterNav from '../included/Footer';
import {styles} from '../pages/styling/style';

export default {
  props: {
    navigation: {
      type: Object
    },
  },
  data() {
    return {
      Platform,
      styles
    };
  },
  components: { FooterNav },
  methods: {
    goToPage: function (page) {
      this.navigation.navigate(page);
    },
    logout() {
      AsyncStorage.removeItem('login');
      this.$root.loggedIn = false;
    }
  },
};
</script>
