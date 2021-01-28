<template>
  <nb-container>
   <nb-header :style="styles.background">
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
			:onDayPress="(day)=>{console.log(day.dateString)}"
			:items="appointments"
			:style="styleAppointments.agenda"
			:theme="{
				backgroundColor:'#fff',
				calendarBackground:'#fff',
				agendaDayTextColor: '#fff',
				dayTextColor:'#0078ae',
				agendaTodayColor:'#0078ae',
				agendaDayNumColor: '#0078ae',
				agendaKnobColor: '#0078ae',
				textSectionTitleColor:'#0078ae',
				selectedDayBackgroundColor: '#0078ae',
				dotColor: '#0078ae',
				dayTextColor: '#2d4150',
  		}"
		/>
    <nb-content padder>
      <nb-card transparent> 
        <nb-card-item header>
          <nb-text>Afspraak maken met {{navigation.getParam('firstname')}} {{navigation.getParam('lastname')}} </nb-text>
        </nb-card-item>
        <nb-card-item>
          <nb-body>
          <nb-body>
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
              <item
                v-for="location in locations"
                :key="location.id"
                :label="location.name"
                :value="location.id"/>
            </nb-picker>
        </nb-card-item>
          </nb-body>            
        </nb-card-item>
        <nb-card-item  footer :style="styles.center">
          <view :style="{ flexDirection:'row' }">
            <nb-button rounded success :on-press="appointmentMake" :style="{marginRight: 5,backgroundColor:'#008551'}">
              <nb-icon active name="checkmark" />
            </nb-button>
            <nb-button rounded danger :on-press="appointmentCancel" :style="{marginLeft: 5}">
              <nb-icon active name="close" />
            </nb-button>
          </view>
        </nb-card-item>
      </nb-card>
    </nb-content>
    <nb-footer>
      <footer-nav :style="{backgroundColor:'#0078ae'}" activeBtn="appointments"></footer-nav>
    </nb-footer>
  </nb-container>
</template>
<script>
  import FooterNav from '../../included/Footer';
  import { Platform,View,Text,TouchableOpacity,Alert} from 'react-native';
  import { Picker } from "native-base";
  import AsyncStorage from '@react-native-async-storage/async-storage';
  import {formatDate,FormatTime,formatDay,formatDateReverse} from "../utils/dates";
	import { Toast } from 'native-base';
	import {fetchData} from "../utils/fetch";
	import { Agenda,LocaleConfig} from 'react-native-calendars';
	import React from 'react'
	import {styleAppointments,styles} from '../styling/style';


	
	
  export default {
    props: {
      navigation: {
        type: Object
      },
      user: {},
    },
    components: { FooterNav,Item: Picker.Item,Agenda },
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
				styles,
				styleAppointments
      };
    },
		mounted() {

			LocaleConfig.locales['nl'] = {
				monthNames: ['januari','februari','maart','april','mei','juni','juli','augustus','September','oktober','November','december'],
				monthNamesShort: ['jan.','feb.','mrt','apr','mei','jun','jul.','aug','sep.','okt.','nov.','dec'],
				dayNames: ['maandag','dinsdag','woensdag','donderdag','vrijdag','zaterdag','zondag'],
				dayNamesShort: ['ma','di','wo','do','vr','za','zo'],
				today: 'vandaag'
			};

			LocaleConfig.defaultLocale = 'nl';
			
			fetchData(`locations`,this.$root.user.token).then(val => {
				this.dataIsReady = true; this.locations = val;
			});
			fetchData('appointment-dates',this.$root.user.token).then(val => {
				this.dataIsReady = true; 
				this.appointments = val;
		});
	},
    methods: {
      goBack: function () {
        this.navigation.goBack();
			},
			renderEmptyDate: function() {
				return (
					 <View style={styleAppointments.emptyDate}>
						<Text>This is empty date</Text>
					</View>
				);
			},
			renderItems: function(item) {
				return (
      	<TouchableOpacity
					style={[styleAppointments.item, {height: item.height}]}
					onPress={() => Alert.alert(item.client)}>
						<View >
							<Text style={{color:'#fff'}}>{item.client}</Text>
							<Text style={{color:'#fff'}}>{item.time}</Text>
							<Text style={{color:'#fff'}}>{item.notes}</Text>
						</View>
					</TouchableOpacity>
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