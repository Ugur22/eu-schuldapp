<template>
  <nb-container>
    <view :style="{ flex: 1,  justifyContent: 'center', alignItems: 'center', padding: 20 }">
      <nb-text :style="{ padding: 10, fontSize: 22 }">{{ $root.lang.t('welcome') }} </nb-text>
        <nb-button full info class="btns" :on-press="() => goToPage('Account')">
          <nb-text class="text-btn">{{ $root.lang.t('my_account') }}</nb-text>
        </nb-button>
        <nb-button full info class="btns" :on-press="() => goToPage('Appointments')">
          <nb-text class="text-btn">{{ $root.lang.t('appointments') }}</nb-text>
        </nb-button>
        <nb-button full info class="btns" :on-press="() => goToPage('Documents')">
          <nb-text class="text-btn">{{ $root.lang.t('check_document') }}</nb-text>
        </nb-button>
        <!-- <nb-button full info class="btns" :on-press="() => goToPage('Help')">
          <nb-text class="text-btn">{{ $root.lang.t('contact') }}</nb-text>
        </nb-button> -->
        <nb-button full info class="btns" :on-press="logout">
          <nb-text class="text-btn">Logout</nb-text>
        </nb-button>
         <nb-text class="text-btn">{{userType}}</nb-text>
    </view>
    <view :style="{ flex: 1,  justifyContent: 'center', alignItems: 'center' }">
      <image :source="require('../../assets/images/logo.png')" />
    </view>
  </nb-container>
</template>

<script>

import { NavigationActions } from 'vue-native-router';
import { AsyncStorage } from "react-native";


export default {
  props: {
    navigation: {
      type: Object
    },
    userType: '',
  },
  data() {
    return {
    };
  },
    computed: {
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
    getUser: async function () {
      let value = '';
      value = await AsyncStorage.getItem('login');
      value = JSON.parse(value);
      return value;
    },
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

<style>
.btns {
  padding:10px;
  background-color:#0078ae;
  margin:10px;
  align-items: center;
  border-radius: 10px;
  justify-content: center;
}

.text-btn {
  font-weight: bold;
}
</style>
