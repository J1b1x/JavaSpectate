# JavaSpectate
A plugin that in- or decreases the player's fly-speed on slot change for PocketMine-MP

#API
### Manually change fly speed:
```php
SessionManager::getInstance()->getSession($player)->setFlySpeed(float $value);
```