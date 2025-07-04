import jQuery from 'jquery';
import TaxSwitchHelper from '../../shared/TaxSwitchHelper';
import TaxSwitchElementBuilder from '../includes/TaxSwitchElementBuilder';

class KapeeTheme{
	constructor( originalTaxDisplay, baseTaxRate ) {
		this.originalTaxDisplay = originalTaxDisplay;
		this.taxRate = baseTaxRate;
		this.taxSwitchElementBuilder = new TaxSwitchElementBuilder(
				this.originalTaxDisplay
		);
		this.vatTexts = null;
	}

	init() {
		const vm = this;
		console.log(window.kapee);
	}
}

export default KapeeTheme;
