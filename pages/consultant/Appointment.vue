<template>
  <nb-container>
    <nb-header :style="{ backgroundColor: '#0078ae' }">
      <nb-left :style="{flex:1}">
        <nb-button transparent :on-press="goBack" >
          <nb-icon name="arrow-back"/>
        </nb-button>
      </nb-left>
      <nb-body :style="{flex:1}">
      	<nb-title>{{ $root.lang.t('appointment') }} detail</nb-title>
      </nb-body>
      <nb-right :style="{flex:1}">
        <nb-button transparent>
          <nb-icon name="information-circle" />
        </nb-button>
      </nb-right>
    </nb-header>
    <nb-content padder>
      <nb-card v-if="dataIsReady">
        <nb-card-item >
          <nb-body :style="{ flex: 1,  justifyContent: 'center', alignItems: 'center' }">
            <nb-text :style="{ fontSize: 24, color: 'green' }">client: {{Appointment.client.firstname}} {{Appointment.client.lastname}}</nb-text>
            <nb-text :style="{ fontSize: 20, color: 'green' }">{{Appointment.title}}</nb-text>
            <nb-card-item>
              <nb-left>
                <nb-text>tijd: {{formatDate(Appointment.event_date)}}</nb-text>
              </nb-left>            
              <nb-right>
                <nb-text>datum: {{FormatTime(Appointment.event_date)}}</nb-text>
              </nb-right>
          </nb-card-item>
          <nb-card-item>
              <nb-text :style="{ fontSize: 20, color: '#000' }">locatie: {{Appointment.location.name}}</nb-text>
          </nb-card-item>
          <nb-card-item v-if="Appointment.notes">
              <nb-text :style="{ fontSize: 14, color: '#000' }">opmerkingen: {{Appointment.notes}}</nb-text>
          </nb-card-item>
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
  import {formatDate,FormatTime} from "../utils/dates";

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
        formatDate,
        FormatTime
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