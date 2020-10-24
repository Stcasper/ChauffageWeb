SELECT R.Piece, CONCAT(ROUND(AVG(T.Humidite),2),"%") as Humidité_Moyenne FROM Temp_releve T, Radiateurs R where R.Numero=T.Radiateur and Date > "2019-03-11" Group by T.Radiateur