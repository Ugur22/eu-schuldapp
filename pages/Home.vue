<template>
  <nb-container>
    <view :style="{ flex: 1,  justifyContent: 'center', alignItems: 'center', padding: 20 }">
      <nb-text :style="{ padding: 10, fontSize: 22 }">{{ $root.lang.t('welcome') }} {{userData.profileData.Voornamen}} {{userData.profileData.Achternaam}} </nb-text>
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
        <nb-list-item>
          <!-- <nb-text :on-press="logout">Logout</nb-text> -->
        </nb-list-item>
    </view>
    <view :style="{ flex: 1,  justifyContent: 'center', alignItems: 'center' }">
      <image :source="require('../assets/images/logo.png')" />
    </view>
  </nb-container>
</template>

<script>

import store from '../store';
import { NavigationActions } from 'vue-native-router';


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
    userData () {
      return store.state.userObj;
    }
  },
  created() {
  },
  methods: {
    goToPage: function (page) {
      this.navigation.navigate(page);
    },
    // logout() {
    //   store.dispatch('LOGOUT', () =>  this.navigation.navigate('Login'))
    // }
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
