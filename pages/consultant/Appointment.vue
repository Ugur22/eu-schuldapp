<template>
  <nb-container>
    <nb-header :style="{backgroundColor:'#0078ae'}">
      <nb-left>
        <nb-button transparent >
          <nb-icon name="arrow-back" :on-press="goBack" />
        </nb-button>
      </nb-left>
      <nb-body>
        <nb-title>Uw afspraak</nb-title>
      </nb-body>
      <nb-right />
    </nb-header>
    <nb-content padder>
      <nb-card v-if="dataIsReady">
        <nb-card-item v-for="appointment in Appointment" :key="appointment.id">
          <nb-body :style="{ flex: 1,  justifyContent: 'center', alignItems: 'center' }">
            <nb-text :style="{ fontSize: 24, color: 'green' }">{{appointment.title}} - {{appointment.location.name}}</nb-text>
            <nb-card-item>
              <nb-left>
                <nb-text>{{appointment.event_date.slice(10,16)}}</nb-text>
              </nb-left>            
              <nb-right>
                <nb-text>{{appointment.event_date.slice(0,11)}}</nb-text>
              </nb-right>
          </nb-card-item>
          <image :source="require('../../assets/images/logo.png')" />
          </nb-body>
        </nb-card-item>
      </nb-card>
      <nb-card-item class="loadingWrapper" v-else>
			  <image :source="require('../../assets/images/loader.gif')" class="loading" />
	   </nb-card-item>
    </nb-content>
    <nb-footer>
      <footer-nav :style="{backgroundColor:'#0078ae'}" activeBtn="appointments"></footer-nav>
    </nb-footer>
  </nb-container>
</template>

<script>
  import FooterNav from '../../included/FooterConsultant';
  import { AsyncStorage } from 'react-native';

  export default {
    props: {
      navigation: {
        type: Object
      },
      user: {},
    },
    components: { FooterNav },
    data() {
      return {
        Appointment:{},
        dataIsReady: false,
      };
    },
    created() {
      this.GetAppointment();
    },
    methods: {
      goBack: function () {
        this.navigation.goBack();
      },
      GetAppointment: async function () {
        let value = '';
        try {
          value = await AsyncStorage.getItem('login');
          this.user = JSON.parse(value);
        } catch (error) {
          // Error retrieving data
          console.log(error.message);
        }

        try {
          let response = await fetch('http://api.arsus.nl/consultant/appointment', {
            method: 'POST',
            headers: {
              accept: 'application/json',
              'Content-Type': 'application/json',
            },
            body: JSON.stringify({
              email: this.user.email,
              password: this.user.password,
              id: this.navigation.getParam('id'),
            }),
          });

          let responseJson = await response.json();
          if (responseJson.success) {
            this.Appointment = responseJson.results;
            this.dataIsReady = true;
          } else {
            console.log(responseJson);
          }
        } catch (error) {
          console.log(error);
          console.error(error);
        }
      },
    }
  }
</script>
<style scoped>
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