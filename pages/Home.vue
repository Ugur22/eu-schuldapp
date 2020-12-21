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
          <nb-text class="text-btn">{{ $root.lang.t('check_documents') }}</nb-text>
        </nb-button>
        <nb-button full info class="btns" :on-press="() => goToPage('Help')">
          <nb-text class="text-btn">{{ $root.lang.t('contact') }}</nb-text>
        </nb-button>
        <nb-button full info class="btns" :on-press="logout">
          <nb-text class="text-btn">Logout</nb-text>
        </nb-button>
    </view>
    <view :style="{ flex: 1,  justifyContent: 'center', alignItems: 'center' }">
      <image :source="require('../assets/images/logo.png')" />
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
    }
  },
  data() {
    return {
      name: 'Mark'
    };
  },
    computed: {
  },
  created() {
  },
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
