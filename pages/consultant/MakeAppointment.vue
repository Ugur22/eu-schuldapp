<template>
  <nb-container>
   <nb-header :style="{backgroundColor:'#0078ae'}">
      <nb-left>
        <nb-button transparent :on-press="goBack">
          <nb-icon name="arrow-back" />
        </nb-button>
      </nb-left>
      <nb-body>
        <nb-title>Afspraak maken</nb-title>
      </nb-body>
    </nb-header>
		<Agenda
			:minDate="formatDateReverse(minimumDate)"
			 :renderEmptyDate="renderEmptyDate"
			 :renderItem="(item) => renderItems(item)"
			 :enableSwipeMonths="true"
			  :items="testItems"
		/>
    <nb-content padder>
      <nb-card> 
        <nb-card-item header>
          <nb-text>Afspraak maken met {{navigation.getParam('firstname')}} {{navigation.getParam('lastname')}} </nb-text>
        </nb-card-item>
        <nb-card-item>
          <nb-body>
          <!-- <nb-body>
            <nb-item :error="(!title.required)" floatingLabel>
              <nb-label>Title</nb-label>
              <nb-input v-model="title" />
            </nb-item>
            <nb-item floatingLabel :style="{  marginTop:10 }">
              <nb-label>Notes</nb-label>
              <nb-input v-model="notes" />
            </nb-item>
            <nb-card-item floatingLabel>
          <nb-label>{{ $root.lang.t('Township') }}:</nb-label>
            <nb-picker
              mode="dialog"
              placeholder="gemeente"
              :selectedValue="selectedLocation"
              :onValueChange="onLocationChange">
              <it	em
                v-for="location in locations"
                :key="location.id"
                :label="location.name"
                :value="location.id"/>
            </nb-picker>
        </nb-card-item>
          </nb-body> -->
            <!-- <nb-text :style="{ fontSize: 20 }">{{ formatDay(date)}}</nb-text>
            <view :style="{ flexDirection:'row', paddingTop:10 }">
              <nb-text :style="{ fontSize: 30 }">{{ formatDate(date) }}</nb-text>
              <nb-button rounded info :on-press="showDatepicker" :style="{ marginLeft: 10,backgroundColor:'#0078ae' }">
                <nb-icon active name="calendar" />
              </nb-button>
            </view> -->
            <!-- <view :style="{ flexDirection:'row',paddingTop:10 }">
              <nb-text :style="{ fontSize: 28 }">{{ FormatTime(date)}}</nb-text>
              <nb-button rounded info :on-press="showTimepicker" :style="{ justifyContent: 'center',marginLeft: 10, alignSelf: 'flex-end',backgroundColor:'#0078ae' }">
                <nb-icon active name="clock"/>
              </nb-button>
            </view> -->
          </nb-body>            
        </nb-card-item>
        <nb-card-item footer :style="{ flex: 1,  backgroundColor: 'transparent', justifyContent: 'center', alignItems: 'center' }">
          <!-- <view :style="{ flexDirection:'row' }">
            <nb-button rounded success :on-press="appointmentMake" :style="{marginRight: 5,backgroundColor:'#008551'}">
              <nb-icon active name="checkmark" />
            </nb-button>
            <nb-button rounded danger :on-press="appointmentCancel" :style="{marginLeft: 5}">
              <nb-icon active name="close" />
            </nb-button>
          </view> -->
        </nb-card-item>
      </nb-card>
      <view>
        <date-time-picker
          v-if="show"
          testID="dateTimePicker"
          :value="date"
          :mode="mode"
          :is24Hour=true
          display="default"
          :minimumDate="minimumDate"
          :minuteInterval="10"
          :onChange="getDateTime"
        />
      </view>
    </nb-content>
    <nb-footer>
      <footer-nav :style="{backgroundColor:'#0078ae'}" activeBtn="appointments"></footer-nav>
    </nb-footer>
  </nb-container>
</template>
<style lang="stylus" scoped>
  emptyDate: {
    height: 15,
    flex: 1,
    paddingTop: 30
  }
</style>
<script>
  import FooterNav from '../../included/Footer';
  import DateTimePicker from '@react-native-community/datetimepicker';
  import { Platform,View,Text} from 'react-native';
  import { Picker } from "native-base";
  import AsyncStorage from '@react-native-async-storage/async-storage';
  import moment from "moment";
  import {formatDate,FormatTime,formatDay,formatDateReverse} from "../utils/dates";
	import { Toast } from 'native-base';
	import {fetchData} from "../utils/fetch";
	import {Calendar, CalendarList, Agenda} from 'react-native-calendars';
	import React from 'react'
  
  export default {
    props: {
      navigation: {
        type: Object
      },
      user: {},
    },
    components: { FooterNav, DateTimePicker,Item: Picker.Item,Agenda },
    data() {
      return {
        date: this.addDays(1),
        mode: 'date',
        show: false,
        minimumDate: this.addDays(0), 
        title:'',
        notes:'',
        locations: {},
        selectedLocation: '0',
        formatDate,
        FormatTime,
				formatDay,
				formatDateReverse,
				appointments: {},
				testItems:{
					'2021-01-29': [{name: 'item 1 - any js object'}],
					'2021-01-27': [{name: 'item 2 - any js object'}],
					'2021-01-28': [{name: 'item 2 - any js object'}],
					'2021-01-30': [{name: 'afspraak met ugur'}],
  			}
      };
    },
    created() {
				moment.locale('nl'); 
		},
		mounted() {
			fetchData(`locations`).then(val => {
				this.dataIsReady = true; this.locations = val;
			});
			fetchData('consultant/appointments').then(val => {
				this.dataIsReady = true; 
				let that = this;
				val.map(function(appointment){
				return that.appointments = {
					[formatDateReverse(appointment.event_date)]:
								[{name: appointment.title}]
						}

			})
			// console.log(this.appointments);
		});
	},
    methods: {
      goBack: function () {
        this.navigation.goBack();
			},
			renderEmptyDate: function() {
				return (
					<View class="emptyDate">
						<Text>This is empty date!</Text>
					</View>
				);
			},
			renderItems: function(item) {
				return (
					<View >
						<Text>{item.name}</Text>
					</View>
				);
			},
      getDateTime: function (event, selectedDate) {
        let currentDate = selectedDate || date;
        this.date = currentDate;
        this.show = Platform.OS === 'ios';
        
      },
      showMode: function (currentMode) {
        this.show = true;
        this.mode = currentMode;
      },
      showDatepicker: function () {
        this.showMode('date');
      },
      showTimepicker: function () {
        this.showMode('time');
      },
      addDays: function (days) {
        let date = new Date();
        date.setDate(date.getDate() + days);
        return date;
      },
      onLocationChange: function (value) {
      this.selectedLocation = value;
    },
      appointmentMake: async function () {
      let value = '';
      try {
        value = await AsyncStorage.getItem('login');
        this.user = JSON.parse(value);
      } catch (error) {
        // Error retrieving data
        console.log(error.message);
      }

      if(this.title){
        try {
          let response = await fetch('http://api.arsus.nl/consultant/make-appointment', {
            method: 'POST',
            headers: {
              accept: 'application/json',
              'Content-Type': 'application/json',
              'Authorization': `Bearer ${this.user.token}`
            },
            body: JSON.stringify({
              email: this.user.email,
              password: this.user.password,
              title: this.title,
              notes: this.notes,
              date: this.formatDate(this.date),
              time: this.FormatTime(this.date),
              client_id: this.navigation.getParam('ClientID'),
              location_id: this.selectedLocation,
            }),
          });

          let responseJson = await response.json();
          if (responseJson.success) {
            	this.navigation.navigate('AppointmentConfirmation', {
              title: this.title,
              notes: this.notes,
              clientName: `${this.navigation.getParam('firstname')} ${this.navigation.getParam('lastname')}`,
              date: this.formatDate(this.date),
              time: this.FormatTime(this.date)
            });
          } else {
            console.log(responseJson);
          }
        } catch (error) {
          console.error(error);
        }
      }else {
          Toast.show({
          text: 'vul een titel in!',
          buttonText: 'ok'
        })
      }
        
      },
      appointmentCancel: function () {
        this.navigation.navigate('Appointments');
      }
    }
  }
</script>