<template>
  <nb-container>
	    <header :pageTitle="$root.lang.t('clients')" :method="goBack" />
	<nb-content >
	  <nb-item :style="{ borderColor: '#62B1F6' }">
		<nb-input v-model="search" :placeholder="$root.lang.t('search')" />
	  </nb-item>
		<nb-item>
				 <nb-text :style="{ display: 'none' }"  class="text">{{getInput}}</nb-text>
			</nb-item>
			<nb-item v-if="Clients.length === undefined && dataIsReady" >
					<nb-text class="text">
						geen resultaten gevonden
					</nb-text>
			</nb-item>
	  <nb-list v-if="dataIsReady">
	  <nb-list-item itemDivider class="list-Header">
		  <nb-left>
			<nb-text  class="text header-text">{{$root.lang.t('name')}}</nb-text>
		  </nb-left>
		  <nb-body>
			<nb-text  class="text header-text">{{$root.lang.t('status')}}</nb-text>
		  </nb-body>
		  <nb-right>
		  </nb-right>
		</nb-list-item>
	  </nb-list>
	  <nb-list v-if="dataIsReady">
		<nb-list-item v-for="client in Clients" :key="client.id" >
		  <nb-left >
			<nb-text  class="text">{{client.firstname}} {{client.lastname}}</nb-text>
		  </nb-left>
		  <nb-body >
				<nb-button :disabled="client.status.status == 'Compleet' ? true : false" iconRight transparent :on-press="() => confirmNextStep(client.id,client.status.sort+1, client.status.status)" >
					<nb-text  :class="client.status.status == 'Compleet' ? 'disabled': 'text'">{{client.status.status}}</nb-text>
					<nb-icon v-if="client.status.status !== 'Compleet'" name="arrow-dropright" :style="{ fontSize: 28, color: '#0078ae'}" />
				</nb-button>
		  </nb-body>
		  <nb-right :on-press="() => detailClient(client.id)">
				<nb-button iconRight transparent :on-press="() => detailClient(client.id)" >
				<nb-icon class="text" name="arrow-forward" />
			</nb-button>
		  </nb-right>
		</nb-list-item>
	  </nb-list>
	  <nb-spinner color="#0078ae" v-else /> 
	</nb-content>
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

.list-Header{
  padding-bottom: 0;
}

.header-text {
  font-weight: bold;
}

.disabled {
	color: grey;
}
</style>

<script>
import { Alert } from "react-native";
import FooterNav from '../../included/Footer';
import Header from '../../included/Header';
import FileClient from './FileClient';
import {fetchData,PostData} from "../utils/fetch";

export default {
  props: {
	navigation: {
	  type: Object,
	},
	user: {},
  },
  components: { FooterNav,FileClient,Header },
  data() {
		return {
			isModalVisible: false,
			Clients: {},
			dataIsReady: false,
			clientStatus: '',
			nextStep: '',
			search:''
		};
  },
	computed: {
		getInput: function(){
			fetchData(`consultant/clients?search=${this.search}`,this.$root.user.token).then(val => {
				this.dataIsReady = true;
				this.Clients = val;
			});
		}
	},
  methods: {
		confirmNextStep: function(clientID,clientStatus,currentStatus){
			fetchData(`client/status`,this.$root.user.token).then(val => {
					let that = this;
				val.map(function(status){
					if(status.sort === clientStatus){
						that.nextStep = status.status;
					}
				})
				Alert.alert(
				`${this.$root.lang.t('confirm_message')}`,
				`huidige stap: ${currentStatus}
volgende stap: ${that.nextStep}`,
				[
					{
						text: `${this.$root.lang.t('no')}`,
						style: 'cancel'
					},
					{ text: `${this.$root.lang.t('yes')}`, onPress: () => 
							PostData(`consultant/client/next-step?client_id=${clientID}`,this.$root.user.token).then(val => {
								if(val.message === "no debt"){
									Alert.alert(
										`${this.$root.lang.t('missing_info')}`,
											`${this.$root.lang.t('missing_info_detail')}`,
										[
											{
												text: 'ok',
												style: 'cancel'
											}
										],
										{ cancelable: false }
									);
								} 
								fetchData(`consultant/clients`,this.$root.user.token).then(val => {
									this.dataIsReady = true;
									this.Clients = val;
							});
						})
					}
				],
				{ cancelable: false }
			);
			});
			

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

