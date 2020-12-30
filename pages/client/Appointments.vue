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
    </nb-header>
    <nb-content padder>
      <nb-card :style="{ marginTop: 20 }" v-if="dataIsReady">
        <nb-card-item header bordered>
          <nb-text class="title">{{ $root.lang.t('appointments') }}</nb-text>
        </nb-card-item>
        <nb-card-item
          v-for="appointment in appointments"
          :key="appointment.id">
            <nb-left>
					    <nb-text class="text">{{ formatDate(appointment.event_date)}}</nb-text>
					    <nb-text class="text">{{ FormatTime(appointment.event_date)}}</nb-text>
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
import FooterNav from '../../included/Footer';
import { AsyncStorage } from 'react-native';
import {formatDate,FormatTime} from "../utils/dates";

export default {
  props: {
    navigation: {
      type: Object,
	},
	user: {},
  },
  data() {
    return {
    addAppointment: false,
	  appointments: {},
    dataIsReady: false,
    formatDate,
    FormatTime
    };
  },
  created() {
    this.getAppointments();
  },
  components: { FooterNav },
  methods: {
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
        let response = await fetch('http://api.arsus.nl/client/appointments ', {
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
  },
};
</script>
