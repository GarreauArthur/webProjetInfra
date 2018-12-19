INSERT INTO Clients
(id, prenom , nom      , mail            , telephone)
VALUES
(1 , 'toto' , 'Tata'   , 'toto@yeet.com' , '0251445357'),
(2 , 'wadu' , 'Hek'    , 'wadu@heck.hek' , '3615-hekistan'),
(3 , 'alice', 'cagliss', 'alice@alice.fr', '0736381736'),
(4 , 'foo'  , 'oof'    , 'foo@oof.foo'   , '0938294638')
;


INSERT INTO Hotels
(id, nom      , adresse                         )
VALUES
(1 , 'Tothell', '29 rue des blagpatrédrol'      ),
(2 , 'Hekel'  , '2 street of hekistant'         ),
(3 , 'Otello' , '23 boulevard Charles Allemagne'),
(4 , 'Hothell', '-18 avenue de yfaicho'         )
;


INSERT INTO Chambres
(numeroChambre, hotel, nbLitSimple, nbLitDouble, prix , gammeChambre, etage)
VALUES
(1            , 1    , 1          , 0           , 2   , 'cheap'     , 1    ),
(2            , 1    , 0          , 1           , 4   , 'cheap'     , 1    ),
(3            , 2    , 1          , 0           , 200 , 'midlle'    , 2    ),
(4            , 2    , 2          , 0           , 350 , 'plus'      , 3    ),
(5            , 2    , 1          , 1           , 90  , 'nice'      , 4    ),
(6            , 3    , 1          , 0           , 150 , 'very nice' , 2    ),
(7            , 3    , 1          , 0           , 100 , 'cheap'     , 9    ),
(8            , 4    , 1          , 0           , 6789, 'premium'   , 18   )
;

INSERT INTO Reservations
(id, dateDebut            , dateFIn               , client, chambre, hotel)
VALUES
(1 , '20181218'::timestamp, '20181219'::timestamp , 1     , 1      , 1    ),
(2 , '20181224'::timestamp, '20181226'::timestamp , 2     , 4      , 2    ),
(3 , '20181230'::timestamp, '20190102'::timestamp , 3     , 6      , 3    ),
(4 , '20190123'::timestamp, '20190128'::timestamp , 4     , 8      , 4    )
;
