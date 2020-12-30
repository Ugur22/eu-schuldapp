<template>
  <nb-container>
    <nb-header :style="{ backgroundColor: '#0078ae' }">
      <nb-left>
        <nb-button transparent>
          <nb-icon name="arrow-back" :on-press="goBack" />
        </nb-button>
      </nb-left>
      <nb-body>
      	<nb-title>{{ $root.lang.t('appointments') }}</nb-title>
      </nb-body>
      <nb-right />
    </nb-header>
    <nb-content padder>
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
      <nb-card :style="{ marginTop: 20 }" v-if="addAppointment"  >
        <nb-card-item header bordered>
          <nb-text class="text">Kies een client om afspraak te maken</nb-text>
        </nb-card-item>
        <nb-card-item v-for="client in Clients" :key="client.id">
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
      <nb-card :style="{ marginTop: 20 }" v-if="dataIsReady">
        <nb-card-item header bordered>
          <nb-text class="title">{{ $root.lang.t('appointments') }}</nb-text>
        </nb-card-item>
        <nb-card-item
          v-for="appointment in appointments"
          :key="appointment.id">
            <nb-left>
				<nb-button transparent :on-press="() => openAppointment(appointment.id)">
					<nb-text class="text">{{ formatDate(appointment.event_date)}}</nb-text>
					<nb-text class="text">{{ formatTime(appointment.event_date)}}</nb-text>
				</nb-button>
            </nb-left>
         	<nb-right>
            	<nb-text class="text">{{ appointment.location.name }}</nb-text>
          	</nb-right>
        </nb-card-item>
      </nb-card>
	   <nb-card-item class="loadingWrapper" v-else>
			<image :source="require('../../assets/images/loader.gif')" class="loading" />
	   </nb-card-item>
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

.loadingWrapper {
  align-items: center;
  justify-content: center;
  flex:1;
}

.loading {
  height:50;
  width:50;
}

.title {
	color: #0078ae;
	font-size:20;
}
</style>
<script>
import FooterNav from '../../included/FooterConsultant';
import { AsyncStorage } from 'react-native';
import moment from "moment";


export default {
  props: {
    navigation: {
      type: Object,
	},
  user: {}
  },
  data() {
    return {
      addAppointment: false,
      appointments: {},
      dataIsReady: false,
      Clients: {},
    };
  },
  created() {
    this.getAppointments();
    this.getClients();
  },
  components: { FooterNav },
  methods: {
    formatDate: function(date) {
      let Formatdate = moment(date).format("DD-MM-YYYY");
      return Formatdate;
    },
    formatTime: function(date) {
      let FormatTime = moment(date).format("HH:mm");
      return FormatTime;
    },
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
