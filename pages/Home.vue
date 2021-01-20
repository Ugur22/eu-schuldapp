<template>
  <nb-container>
      <nb-header :style="{elevation:0,backgroundColor: '#FFF'}">
        <nb-right :style="{flex:1}">
          <nb-button transparent :on-press="logout">
            <nb-icon :style="{ color: '#0078ae',fontSize:32 }" name="exit" />
          </nb-button>
        </nb-right>
      </nb-header>
    <view v-if="userType === 'client'" :style="{ flex: 1,  justifyContent: 'center', alignItems: 'center', padding: 20,marginTop:-70 }">
          <nb-text :style="{ padding: 10, fontSize: 22 }">{{ $root.lang.t('welcome') }} {{name}}</nb-text>
        <nb-button full info class="btns" :on-press="() => goToPage('Account')">
          <nb-text class="text-btn">{{ $root.lang.t('my_account') }}</nb-text>
        </nb-button>
        <nb-button full info class="btns" :on-press="() => goToPage('Appointments')">
          <nb-text class="text-btn">{{ $root.lang.t('appointments') }}</nb-text>
        </nb-button>
        <nb-button full info class="btns" :on-press="() => goToPage('Documents')">
          <nb-text class="text-btn">{{ $root.lang.t('check_document') }}</nb-text>
        </nb-button>
    </view>
    <view v-else :style="{ flex: 1,  justifyContent: 'center', alignItems: 'center', padding: 20, marginTop:-110  }">
      <nb-text :style="{ padding: 10, fontSize: 22 }">{{ $root.lang.t('welcome') }} {{name}}</nb-text>
        <nb-button full info class="btns" :on-press="() => goToPage('Clients')">
          <nb-text class="text-btn">{{ $root.lang.t('clients') }}</nb-text>
        </nb-button>
        <nb-button full info class="btns" :on-press="() => goToPage('AppointmentsConsultant')">
          <nb-text class="text-btn">{{ $root.lang.t('appointments') }}</nb-text>
        </nb-button>
    </view>
    <view :style="{ flex: 1,  justifyContent: 'center', alignItems: 'center' }">
      <image :source="require('../assets/images/logo.png')" />
    </view>
      <nb-footer>
      <footer-nav
        :style="{ backgroundColor: '#0078ae' }"
        activeBtn="home"
      ></footer-nav>
    </nb-footer>
  </nb-container>
</template>
<script>

import { NavigationActions } from 'vue-native-router';
import { AsyncStorage } from "react-native";
import FooterNav from '../included/Footer';


export default {
  props: {
    navigation: {
      type: Object
    },
    userType: '',
    name:''
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
      this.name = val.name
    }).catch(e => {
      // error
      console.log(e);
    });
  },
  components: { FooterNav },
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
