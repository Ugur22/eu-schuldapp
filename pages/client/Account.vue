<template>
  <nb-container>
    <nb-header :style="{ backgroundColor: '#0078ae' }">
      <nb-left>
        <nb-button transparent  :on-press="goBack">
          <nb-icon name="arrow-back" />
        </nb-button>
      </nb-left>
      <nb-body>
        <nb-title>{{ $root.lang.t('my_account') }}</nb-title>
      </nb-body>
      <nb-right>
      <nb-right />
    </nb-header>
    <nb-content padder>
      <nb-card v-if="dataIsReady">
        <nb-card-item
          header
          bordered
          :style="{ flex: 1, justifyContent: 'center', alignItems: 'center' }">
          <nb-text class="text-account header"
            >{{clientData.firstname}} {{clientData.lastname}}</nb-text >
        </nb-card-item>
        <nb-card-item>
          <nb-body>
            <nb-card-item >
              <nb-left>
                <nb-icon :style="{ color: '#0078ae' }" name="person"></nb-icon>
                <nb-text class="text-account">{{ $root.lang.t('BSN') }}: {{clientData.social_security_id}}</nb-text>
              </nb-left>
            </nb-card-item>
            <nb-card-item >
              <nb-left>
                <nb-icon :style="{ color: '#0078ae' }" name="pin"></nb-icon>
                <nb-text class="text-account">{{clientData.address}}{{ "\n" }}{{clientData.postal_code}} {{clientData.birth_place}}</nb-text>
              </nb-left>
            </nb-card-item>
            <nb-card-item >
              <nb-left>
                <nb-icon :style="{ color: '#0078ae' }" name="mail"></nb-icon>
                <nb-text class="text-account">{{ clientData.user.email }}</nb-text>
              </nb-left>
            </nb-card-item>
            <nb-card-item >
              <nb-left>
                <nb-icon :style="{ color: '#0078ae' }" name="call"></nb-icon>
                <nb-text class="text-account">{{clientData.phonenumber}}</nb-text>
              </nb-left>
            </nb-card-item>
          </nb-body>
        </nb-card-item>
        <nb-card-item footer> </nb-card-item>
      </nb-card>
        <nb-spinner color="#0078ae" v-else />
    </nb-content>
    <nb-footer>
      <footer-nav
        :style="{ backgroundColor: '#0078ae' }"
        activeBtn="account"
      ></footer-nav>
    </nb-footer>
  </nb-container>
</template>
<script>
import FooterNav from '../../included/Footer';
import AsyncStorage from '@react-native-async-storage/async-storage';

export default {
  props: {
    navigation: {
      type: Object,
      user: {},
    },
  },
  components: { FooterNav },
  data() {
    return {
     clientData: {},
      dataIsReady: false
    };
  },
  created() {
    this.userData();
   
  },
  methods: {
    userData: async function () {
      let value = '';
      try {
        value = await AsyncStorage.getItem('login');
        this.user = JSON.parse(value);
      } catch (error) {
        // Error retrieving data
        console.log(error.message);
      }

      try {
        let response = await fetch('http://api.arsus.nl/client', {
          method: 'GET',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${this.user.token}`
          }
        });

        let responseJson = await response.json();
        if (responseJson.success) {
          this.clientData = responseJson.results;
          this.dataIsReady = true;
        } else {
          console.log(responseJson);
        }
      } catch (error) {
        console.log(error);
        console.error(error);
      }
    },
    goBack: function () {
      this.navigation.goBack();
    },
    goToPage: function (page) {
      this.navigation.navigate(page);
    },
  },
};
</script>
<style>
.text-account {
  color: #0078ae;
}
.header {
  font-weight: bold;
  font-size: 24px;
}


</style>
