<template>
  <nb-container>
    <nb-header :style="{ backgroundColor: '#0078ae' }">
      <nb-left :style="{flex:1}">
        <nb-button transparent :on-press="goBack" >
          <nb-icon name="arrow-back"/>
        </nb-button>
      </nb-left>
      <nb-body :style="{flex:1}">
      	<nb-title>details</nb-title>
      </nb-body>
      <nb-right :style="{flex:1}">
        <nb-button transparent>
          <nb-icon name="information-circle" />
        </nb-button>
      </nb-right>
    </nb-header>
    <nb-content>
      <nb-card v-if="dataIsReady" class="debtCard"
        :style="{
          marginLeft: 10,
          marginRight: 10,
          marginTop: 10,
          marginBottom: 10,
          backgroundColor: '#0078ae'
        }"
      >
        <nb-card-item rounded :style="{ backgroundColor: '#0078ae' }">
          <nb-body>
            <nb-grid class="marginBottom">
              <nb-col>
                <nb-text class="headerText">title</nb-text>
                <nb-text class="detailText">{{otherDoc.title}}</nb-text>
              </nb-col>
              <nb-col>
                <nb-text class="headerText">date</nb-text>
                <nb-text class="detailText">{{otherDoc.doc_date_time}}</nb-text>
              </nb-col>
            </nb-grid>
          </nb-body>
        </nb-card-item>
      </nb-card>
      <nb-card-item class="loadingWrapper" v-else>
			  <image :source="require('../../assets/images/loader.gif')" class="loading" />
	   </nb-card-item>
    </nb-content>
  </nb-container>
</template>

<script>
import AsyncStorage from '@react-native-async-storage/async-storage';

export default {
  props: {
    navigation: {
      type: Object,
    },
    user: {},
  },
  data() {
    return {
      dataIsReady: false,
      otherDoc: {},
    };
  },
  created() {
    this.GetOther();
  },
  methods: {
    GetOther: async function () {
      let value = '';
      try {
        value = await AsyncStorage.getItem('login');
        this.user = JSON.parse(value);
      } catch (error) {
        // Error retrieving data
        console.log(error.message);
      }

      try {
        let response = await fetch('http://api.arsus.nl/consultant/doc/other', {
          method: 'POST',
          headers: {
            accept: 'application/json',
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            email: this.user.email,
            password: this.user.password,
            client_id: this.navigation.getParam('ClientID'),
            id: this.navigation.getParam('docID'),
          }),
        });

        let responseJson = await response.json();
        if (responseJson.success) {
          this.otherDoc = responseJson.results;
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
  },
};
</script>
<style>
.headerText {
  color: white;
  font-weight: bold;
  font-size: 20px;
}
.detailText {
  color: white;
  font-size: 16px;
}

.marginBottom {
  margin-bottom: 20px;
}
.debtCard {
  border-radius: 15px;
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
