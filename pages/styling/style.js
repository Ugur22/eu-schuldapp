

import { StyleSheet } from 'react-native';

export const styles = StyleSheet.create({
	text: {
		color: '#0078ae'
	},
	background: {
		backgroundColor: '#0078ae'
	},
	header: {
		fontWeight: 'bold',
		color: '#0078ae',
		fontSize: 24
	},
	footer: {
		backgroundColor: '#0078ae'
	},
	center: {
		flex: 1, 
		justifyContent: 'center',
		alignItems: 'center'
	}
});

export const styleAppointments = StyleSheet.create({
	item: {
		backgroundColor: '#0078ae',
		flex: 1,
		borderRadius: 5,
		padding: 10,
		marginRight: 10,
		marginTop: 17,
	},
	emptyDate: {
		padding: 10,
		marginRight: 10,
		marginTop: 17,
	},
	agenda: {
		height: 350,
	}
});