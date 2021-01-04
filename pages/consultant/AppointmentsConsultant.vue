<template>
  <nb-container>
    <nb-header :style="{ backgroundColor: '#0078ae' }">
      <nb-left :style="{flex:1}">
        <nb-button transparent :on-press="goBack" >
          <nb-icon name="arrow-back"/>
        </nb-button>
      </nb-left>
      <nb-body :style="{flex:1}">
      	<nb-title>{{ $root.lang.t('appointments') }}</nb-title>
      </nb-body>
      <nb-right :style="{flex:1}">
        <nb-button transparent>
          <nb-icon name="information-circle" />
        </nb-button>
      </nb-right>
    </nb-header>
    <nb-content padder >
      <nb-card :style="{ marginTop: 10 }" v-if="addAppointment"  >
        <nb-card-item header bordered>
          <nb-text class="text">Kies een client om afspraak te maken</nb-text>
        </nb-card-item>
        <nb-card-item bordered v-for="client in Clients" :key="client.id">
          <nb-body>
            <nb-button transparent :on-press="() => makeAppointment(client.id,client.firstname,client.lastname)">
              <nb-text class="text">{{client.firstname}} {{client.lastname}}</nb-text>
            </nb-button>
          </nb-body>
          <nb-right>
            <nb-text class="text">{{client.status}}</nb-text>
          </nb-right>
        </nb-card-item>
      </nb-card>
      <nb-card :style="{ marginTop: addAppointment ? 10 : 50 }" v-if="dataIsReady">
        <nb-card-item header bordered>
          <nb-text class="text">Alle afspraken</nb-text>
        </nb-card-item>
        <nb-list>
          <nb-list-item  v-for="appointment in appointments"
            :key="appointment.id"  :style="{ padding: 0}" :on-press="() => openAppointment(appointment.id)">
            <nb-left>
              <nb-body>
                <nb-text class="text">{{ formatDate(appointment.event_date)}}</nb-text>
                <nb-text class="text">{{ FormatTime(appointment.event_date)}}</nb-text>
              </nb-body>
            </nb-left>
            <nb-body>
                <nb-text class="text">{{appointment.client.firstname}} {{appointment.client.lastname}} </nb-text>
                <nb-text class="text">{{ appointment.location.name }}</nb-text>
            </nb-body>
            <nb-right>
              <nb-icon class="text" name="arrow-forward" />
            </nb-right>
          </nb-list-item>
        </nb-list>
      </nb-card>
      <nb-spinner color="#0078ae" v-else /> 
    <nb-button v-if="dataIsReady"
        rounded
        info
        :style="{
          backgroundColor: '#0078ae',
          position: 'absolute',
          zIndex: 5,
          elevation: 5,
          right: 5,
          top: 5,
        }"
        :on-press="clickAddAppointment"
      >
        <nb-icon active :name='addAppointment ? "remove" : "add"'/>
      </nb-button>
    </nb-content>
    <nb-footer>
      <footer-nav
        :style="{ backgroundColor: '#0078ae' }"
        activeBtn="appointments"
      ></footer-nav>
    </nb-footer>
  </nb-container>
</template>
<style>
.text {
  color: #0078ae;
}

.header {
	color:#fff;
}



.title {
	color: #0078ae;
	font-size:20;
}
</style>
<script>
import FooterNav from '../../included/Footer';
import AsyncStorage from '@react-native-async-storage/async-storage';
import {formatDate,FormatTime} from "../utils/dates";


export default {
  props: {
    navigation: {
      type: Object,
	},
  user: {}
  },
  components: { FooterNav },
  data() {
    return {
      addAppointment: false,
      appointments: {},
      dataIsReady: false,
      Clients: {},
      formatDate,
      FormatTime
    };
  },
  mounted() {
    this.getAppointments();
    this.getClients();
  },
  components: { FooterNav },
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
        method: 'GET',
        headers: {
          accept: 'application/json',
          'Authorization': `Bearer ${this.user.token}`
        },
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
    getAppointments: async function () {
      let value = '';
      try {
        value = await AsyncStorage.getItem('login');
        this.user = JSON.parse(value);
      } catch (error) {
        // Error retrieving data
        console.log(error.message);
      }

      try {
        let response = await fetch('http://api.arsus.nl/consultant/appointments', {
          method: 'GET',
          headers: {
            accept: 'application/json',
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${this.user.token}`
          }
        });

        let responseJson = await response.json();
        if (responseJson.success) {
          this.appointments = responseJson.results;
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
    makeAppointment: function (id,firstname,lastname) {
      this.navigation.navigate('MakeAppointment', {
        ClientID:id,
        firstname:firstname,
        lastname:lastname
      });
      },
    openAppointment: function (id) {
      this.navigation.navigate('Appointment',{
        id:id
      });
    },
    clickAddAppointment: function (page) {
      this.addAppointment = !this.addAppointment;
    },
  },
};
</script>
