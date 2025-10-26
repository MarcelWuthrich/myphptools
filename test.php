<?php

declare(strict_types=1);

class Pont
{
  // $unite ne sert que dans la classe, on met cette propriété en privé.
   private string $unite = ' m²';
  
   public float $longueur;
   public float $largeur;
  
   public function getSurface(): string
   {
       return ($this->longueur * $this->largeur) . $this->unite; // on renvoie l’unité en plus de la surface
   }}

$towerBridge = new Pont;
$towerBridge->longueur = 286.0;
$towerBridge->largeur = 15.0;

echo $towerBridge->getSurface();

?>