# metric_w3c_html

Metric for the w3c html validator

## Install

1. Clone this repo to `plugins/metric_w3c_html`
2. Run `composer install` in `plugins/metric_w2c_html`
3. Add the following to the `Config::set('PLUGINS', ` array and customize as needed. This will make SiteMaster aware of the plugin but not apply it to any groups.
```
'metric_w3c_html' => [
  // 'weight' => 20, // Adjust weight if desired
  // 'service_url' => 'https://validator.unl.edu/', // adjust the service URL if desired
],
```
4. Add the following your group configuration under the `METRICS` key. This will enable it for the specified group. All metric configuration will override the defaults set in the `'PLUGINS'` array.
```
'metric_w3c_html' => [
  // 'weight' => 20, // Adjust weight if desired
  // 'service_url' => 'https://validator.unl.edu/', // adjust the service URL if desired
],
```
5. Run `php scripts/update.php` from the SiteMaster root directory to install the plugin.

## Overrides for HTML validator

Overrides are found in `src/Metric.php` in the `allowError` function. The text to match should be the same as the text
from the validator website, e.g. "CSS: “ascent-override”: Property “ascent-override” doesn't exist.".
