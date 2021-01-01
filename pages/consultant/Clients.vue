<template>
  <nb-container>
    <nb-header :style="{ backgroundColor: '#0078ae' }">
      <nb-left :style="{flex:1}">
        <nb-button transparent :on-press="goBack" >
          <nb-icon name="arrow-back"/>
        </nb-button>
      </nb-left>
      <nb-body :style="{flex:1}">
      	<nb-title>{{ $root.lang.t('clients') }}</nb-title>
      </nb-body>
      <nb-right :style="{flex:1}">
        <nb-button transparent>
          <nb-icon name="information-circle" />
        </nb-button>
      </nb-right>
    </nb-header>
    <nb-content v-if="dataIsReady">
      <nb-item :style="{ borderColor: '#62B1F6' }">
        <nb-input placeholder="Search" />
      </nb-item>
      <nb-list>
      <nb-list-item itemDivider class="list-Header">
          <nb-left>
            <nb-text  class="text header-text">Naam</nb-text>
          </nb-left>
          <nb-body>
            <nb-text  class="text header-text">status</nb-text>
          </nb-body>
          <nb-right>
            <nb-text  class="text header-text">more</nb-text>
          </nb-right>
        </nb-list-item>
      </nb-list>
      <nb-list >
        <nb-list-item v-for="client in Clients" :key="client.id">
          <nb-left>
            <nb-text  class="text">{{client.firstname}} {{client.lastname}}</nb-text>
          </nb-left>
          <nb-body>
            <nb-text class="text">{{client.status}}</nb-text>
          </nb-body>
          <nb-right>
          <nb-button iconLeft transparent :on-press="() => detailClient(client.id)">
            <nb-icon class="text" name="arrow-forward" />
          </nb-button>
          </nb-right>
        </nb-list-item>
      </nb-list>
    </nb-content>
    <nb-card-item class="loadingWrapper" v-else>
			  <image :source="require('../../assets/images/loader.gif')" class="loading" />
	  </nb-card-item>
    <nb-footer>
      <footer-nav
        :style="{ backgroundColor: '#0078ae' }"
        activeBtn="clients"
      ></footer-nav>
    </nb-footer>
  </nb-container>
</template>

<style>
.headerText {
  color: white;
  font-weight: bold;
}
.detailText {
  color: white;
}
.marginBottom {
  margin-bottom: 20px;
}
.text {
  color: #0078ae;
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

.list-Header{
  padding-bottom: 0;
}

.header-text {
  font-weight: bold;
}
</style>

<script>
import Modal from 'react-native-modal';
import FooterNav from '../../included/FooterConsultant';
import FileClient from './FileClient';
import AsyncStorage from '@react-native-async-storage/async-storage';

export default {
  props: {
    navigation: {
      type: Object,
    },
    user: {},
  },
  components: { FooterNav,FileClient },
  data() {
    return {
      isModalVisible: false,
      Clients: {},
      dataIsReady: false,
    };
  },
  created() {
    this.getClients();
  },
  methods: {
    getClients: async function () {
      let value = '';
      try {
        value = await AsyncStorage.getItem('login');
        this.user = JSON.parse(value);
      } catch (error) {
        // Error retrieving data
        console.log(error.message);
      }

      try {
        let response = await fetch('http://api.arsus.nl/consultant/clients', {
          method: 'POST',
          headers: {
            accept: 'application/json',
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            email: this.user.email,
            password: this.user.password,
          }),
        });

        let responseJson = await response.json();
        if (responseJson.success) {
          this.Clients = responseJson.results;
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
    detailClient: function (id) {
    this.navigation.navigate('FileClient', {
        clientID: id
      });
    },
  },
};
</script>

