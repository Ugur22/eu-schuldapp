<template>
  <nb-container>
    <nb-header :style="{ backgroundColor: '#0078ae' }">
      <nb-left>
        <nb-button transparent>
          <nb-icon name="arrow-back" :on-press="goBack" />
        </nb-button>
      </nb-left>
      <nb-body>
        <nb-title>{{ $root.lang.t('my_account') }}</nb-title>
      </nb-body>
      <nb-right />
    </nb-header>
    <nb-content padder>
      <nb-card v-if="dataIsReady">
        <nb-card-item
          header
          bordered
          :style="{ flex: 1, justifyContent: 'center', alignItems: 'center' }">
          <nb-text class="text-account header"
            >{{clientData.Voornamen}} {{clientData.Achternaam}}</nb-text >
        </nb-card-item>
        <nb-card-item>
          <nb-body>
            <nb-card-item >
              <nb-left>
                <nb-icon :style="{ color: '#0078ae' }" name="person"></nb-icon>
                <nb-text class="text-account">{{ $root.lang.t('BSN') }}: {{clientData.BSN}}</nb-text>
              </nb-left>
            </nb-card-item>
            <nb-card-item >
              <nb-left>
                <nb-icon :style="{ color: '#0078ae' }" name="map"></nb-icon>
                <nb-text class="text-account">{{clientData.Adres}}{{ "\n" }}{{clientData.Postcode}} {{clientData.Woonplaats}}</nb-text>
              </nb-left>
            </nb-card-item>
            <nb-card-item >
              <nb-left>
                <nb-icon :style="{ color: '#0078ae' }" name="mail"></nb-icon>
                <nb-text class="text-account">{{ clientData.email }}</nb-text>
              </nb-left>
            </nb-card-item>
            <nb-card-item >
              <nb-left>
                <nb-icon :style="{ color: '#0078ae' }" name="call"></nb-icon>
                <nb-text class="text-account">{{clientData.Telefoon_Mob}}</nb-text>
              </nb-left>
            </nb-card-item>
          </nb-body>
        </nb-card-item>
        <nb-card-item footer> </nb-card-item>
      </nb-card>
      <nb-card-item class="loadingWrapper" v-else>
			  <image :source="require('../assets/images/loader.gif')" class="loading" />
	   </nb-card-item>
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
import FooterNav from '../included/Footer';
import { AsyncStorage } from 'react-native';

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
          method: 'POST',
          headers: {
            Accespt: 'application/json',
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            email: this.user.email,
            password: this.user.password,
          }),
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

.loadingWrapper {
  align-items: center;
  justify-content: center;
  flex:1;
}

.loading {
  height:50;
  width:50;
}
</style>
