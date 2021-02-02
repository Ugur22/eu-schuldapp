<template>
  <nb-container>
    <header :pageTitle="$root.lang.t('appointments')" :method="goBack" />
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
            <nb-text class="text">{{client.status.status}}</nb-text>
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
import Header from '../../included/Header';
import {formatDate,FormatTime} from "../utils/dates";
import {fetchData} from "../utils/fetch";

export default {
  props: {
    navigation: {
      type: Object,
		}
  },
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
		  fetchData('consultant/appointments',this.$root.user.token).then(val => {
				this.dataIsReady = true; this.appointments = val;});
			fetchData('consultant/clients',this.$root.user.token).then(val => {
				this.dataIsReady = true; this.Clients = val;});
  },
   components: { FooterNav,Header},
  methods: {
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
